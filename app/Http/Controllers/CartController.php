<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController
{
    /**
     * Exibir o carrinho de compras.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return view('cart.index', [
            'cart' => $cart,
            'total' => $total,
            'count' => count($cart)
        ]);
    }

    /**
     * Adicionar item ao carrinho.
     */
    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $productName = $request->input('product_name');
        $productPrice = $request->input('product_price');
        $quantity = $request->input('quantity', 1);
        $productImage = $request->input('product_image', '/img/placeholder.png');
        
        $cart = session()->get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $productId,
                'name' => $productName,
                'price' => $productPrice,
                'quantity' => $quantity,
                'image' => $productImage
            ];
        }
        
        session()->put('cart', $cart);
        
        return redirect()->route('cart.index')->with('success', 'Produto adicionado ao carrinho!');
    }

    /**
     * Remover item do carrinho.
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        
        return redirect()->route('cart.index')->with('success', 'Item removido do carrinho!');
    }

    /**
     * Atualizar quantidade do item no carrinho.
     */
    public function update(Request $request, $id)
    {
        $quantity = $request->input('quantity', 1);
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id]) && $quantity > 0) {
            $cart[$id]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }
        
        return redirect()->route('cart.index')->with('success', 'Carrinho atualizado!');
    }

    /**
     * Limpar carrinho.
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Carrinho limpo!');
    }
}
