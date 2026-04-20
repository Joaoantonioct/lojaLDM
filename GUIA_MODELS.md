# Guia de Models - Loja Virtual LDM

## Model Category (Exemplo Completo)

### 📁 Arquivos Criados:
- **Migration**: `database/migrations/2026_04_19_062651_create_categories_table.php`
- **Model**: `app/Models/Category.php`

---

## 🗂️ Estrutura da Migration

### Campos da Tabela `categories`:
```php
$table->id();                    // ID auto-incremento
$table->string('name');          // Nome da categoria
$table->string('slug')->unique(); // URL amigável única
$table->text('description')->nullable(); // Descrição opcional
$table->string('image')->nullable(); // Caminho da imagem
$table->boolean('is_active')->default(true); // Status ativo/inativo
$table->integer('sort_order')->default(0); // Ordem de exibição
$table->timestamps();           // created_at e updated_at
```

---

## 🏗️ Estrutura do Model

### 1. **Propriedades Básicas**
```php
use HasFactory;                 // Para factories
protected $fillable = [...];    // Campos atribuíveis em massa
protected $casts = [...];       // Conversão de tipos
```

### 2. **Relacionamentos**
```php
public function products(): HasMany
{
    return $this->hasMany(Product::class);
}
```

### 3. **Accessors (Getters Personalizados)**
```php
public function getImageUrlAttribute(): string
{
    // Retorna URL completa da imagem
}
```

### 4. **Mutators (Setters Personalizados)**
```php
public function setNameAttribute($value): void
{
    // Gera slug automaticamente
    $this->attributes['slug'] = Str::slug($value);
}
```

### 5. **Scopes (Consultas Reutilizáveis)**
```php
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

public function scopeOrdered($query)
{
    return $query->orderBy('sort_order')->orderBy('name');
}
```

### 6. **Métodos Personalizados**
```php
public function getProductsCountAttribute(): int
{
    return $this->products()->count();
}

public function hasProducts(): bool
{
    return $this->products()->exists();
}
```

---

## 📋 Models que Você Precisa Criar

### 1. **Product** (Produto)
- **Migration**: `products`
- **Campos**: name, slug, description, price, stock, image, category_id, is_active
- **Relacionamentos**: belongsTo(Category), hasMany(OrderItem)

### 2. **Cart** (Carrinho)
- **Migration**: `carts`
- **Campos**: user_id, product_id, quantity, price
- **Relacionamentos**: belongsTo(User), belongsTo(Product)

### 3. **Order** (Pedido)
- **Migration**: `orders`
- **Campos**: user_id, total, status, shipping_address
- **Relacionamentos**: belongsTo(User), hasMany(OrderItem)

### 4. **OrderItem** (Item do Pedido)
- **Migration**: `order_items`
- **Campos**: order_id, product_id, quantity, price
- **Relacionamentos**: belongsTo(Order), belongsTo(Product)

---

## 🚀 Comandos para Criar

```bash
# Criar Model com Migration
php artisan make:model Product -m
php artisan make:model Cart -m
php artisan make:model Order -m
php artisan make:model OrderItem -m

# Rodar as migrations
php artisan migrate

# Criar Factory para testes
php artisan make:factory CategoryFactory --model=Category
```

---

## 🎯 Padrões a Seguir

### **Fillable Array**
Sempre defina `$fillable` com campos que podem ser atribuídos em massa:
```php
protected $fillable = [
    'name',
    'slug',
    'description',
    // ... outros campos
];
```

### **Casts Array**
Defina conversão de tipos para dados específicos:
```php
protected $casts = [
    'is_active' => 'boolean',
    'price' => 'decimal:2',
    'created_at' => 'datetime',
];
```

### **Relacionamentos**
Use tipos específicos para relacionamentos:
```php
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, BelongsToMany};

public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}
```

### **Scopes**
Crie scopes para consultas comuns:
```php
public function scopeActive($query) { ... }
public function scopeFeatured($query) { ... }
public function scopeByCategory($query, $categoryId) { ... }
```

---

## ✅ Próximos Passos

1. **Criar Product Model** (segundo este guia)
2. **Criar Cart Model**
3. **Criar Order Model**
4. **Criar OrderItem Model**
5. **Rodar migrations**: `php artisan migrate`
6. **Criar Controllers** para cada Model

**Use este Category Model como referência para os outros Models!**
