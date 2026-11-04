<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CatalogProductSearch
{
    private const CLOTHING_TERMS = [
        'roupa', 'roupas', 'vestuario', 'vestimenta', 'vestimentas', 'moda',
    ];

    private const SEARCH_GROUPS = [
        [
            'calçado', 'calçados', 'calcado', 'calcados', 'sapato', 'sapatos', 'tênis', 'tenis',
            'sneaker', 'sneakers', 'bota', 'botas', 'sandália', 'sandálias', 'sandalia',
            'sandalias', 'chinelo', 'chinelos', 'sapatilha', 'sapatilhas', 'rasteirinha',
            'rasteirinhas', 'scarpin', 'mocassim', 'coturno',
        ],
        [
            'blusa', 'blusas', 'camisa', 'camisas', 'camiseta', 'camisetas', 'regata',
            'regatas', 'cropped', 'croppeds', 'top', 'tops',
        ],
        [
            'calça', 'calças', 'calca', 'calcas', 'jeans', 'legging', 'leggings', 'short',
            'shorts', 'bermuda', 'bermudas', 'saia', 'saias',
        ],
        ['vestido', 'vestidos', 'macacão', 'macacões', 'macacao', 'macacoes'],
        ['jaqueta', 'jaquetas', 'casaco', 'casacos', 'moletom', 'moletons', 'blazer', 'blazers'],
    ];

    public function apply(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        $normalizedSearch = $this->normalize($search);
        $tokens = preg_split('/\s+/u', $normalizedSearch, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $meaningfulTokens = array_values(array_diff($tokens, ['em', 'geral', 'de', 'do', 'da', 'para']));
        $isGeneralClothingSearch = count($meaningfulTokens) === 1
            && in_array($meaningfulTokens[0], self::CLOTHING_TERMS, true);

        $expandedTerms = [];
        if ($isGeneralClothingSearch) {
            $expandedTerms = array_merge(array_merge(...self::SEARCH_GROUPS), [
                'uniforme', 'uniformes', 'lingerie', 'biquíni', 'biquínis', 'biquini', 'biquinis',
                'meia', 'meias', 'cueca', 'cuecas', 'sutiã', 'sutiãs', 'sutia', 'sutias',
            ]);
        } elseif (count($meaningfulTokens) === 1) {
            foreach (self::SEARCH_GROUPS as $group) {
                if (in_array($meaningfulTokens[0], array_map(
                    fn (string $term) => $this->normalize($term),
                    $group,
                ), true)) {
                    $expandedTerms = $group;
                    break;
                }
            }
        }

        $expandedTerms = array_values(array_unique($expandedTerms));
        $apparelCategoryIds = $isGeneralClothingSearch
            ? Category::query()->with('parent')->get()
                ->filter(fn (Category $category) => $category->isApparelCategory())
                ->modelKeys()
            : [];
        $matchingCategoryIds = $expandedTerms === [] || $isGeneralClothingSearch
            ? []
            : $this->categoryIdsMatching($expandedTerms);

        return $query->where(function (Builder $matches) use (
            $search,
            $expandedTerms,
            $apparelCategoryIds,
            $matchingCategoryIds,
        ) {
            $this->whereTextMatches($matches, array_values(array_unique([
                $search,
                ...$expandedTerms,
            ])));

            if ($apparelCategoryIds !== []) {
                $matches->orWhereIn('category_id', $apparelCategoryIds);
            }

            if ($matchingCategoryIds !== []) {
                $matches->orWhereIn('category_id', $matchingCategoryIds);
            }
        });
    }

    private function whereTextMatches(Builder $query, array $terms): void
    {
        $query->where(function (Builder $fields) use ($terms) {
            $fields->where(function (Builder $name) use ($terms) {
                $this->addLikeAlternatives($name, 'name', $terms);
            })->orWhere(function (Builder $description) use ($terms) {
                $this->addLikeAlternatives($description, 'description', $terms);
            })->orWhereHas('brand', function (Builder $brand) use ($terms) {
                $this->addLikeAlternatives($brand, 'name', $terms);
            })->orWhereHas('category', function (Builder $category) use ($terms) {
                $category->where(function (Builder $name) use ($terms) {
                    $this->addLikeAlternatives($name, 'name', $terms);
                })->orWhereHas('parent', function (Builder $parent) use ($terms) {
                    $this->addLikeAlternatives($parent, 'name', $terms);
                });
            });
        });
    }

    private function addLikeAlternatives(Builder $query, string $column, array $terms): void
    {
        $query->where(function (Builder $matches) use ($column, $terms) {
            foreach ($terms as $index => $term) {
                if ($index === 0) {
                    $matches->where($column, 'like', "%{$term}%");
                } else {
                    $matches->orWhere($column, 'like', "%{$term}%");
                }
            }
        });
    }

    private function categoryIdsMatching(array $terms): array
    {
        $normalizedTerms = array_values(array_unique(array_map(
            fn (string $term) => $this->normalize($term),
            $terms,
        )));

        return Category::query()->with('parent')->get()
            ->filter(function (Category $category) use ($normalizedTerms) {
                $name = $this->normalize(implode(' ', array_filter([
                    $category->name,
                    $category->slug,
                    $category->parent?->name,
                    $category->parent?->slug,
                ])));

                foreach ($normalizedTerms as $term) {
                    if (str_contains($name, $term)) {
                        return true;
                    }
                }

                return false;
            })
            ->modelKeys();
    }

    private function normalize(string $value): string
    {
        return trim(Str::ascii(mb_strtolower($value)));
    }
}
