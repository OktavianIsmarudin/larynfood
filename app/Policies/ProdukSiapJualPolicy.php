<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ProdukSiapJual;

class ProdukSiapJualPolicy
{
    /**
     * Determine if user can view the product
     */
    public function view(User $user, ProdukSiapJual $produk): bool
    {
        return $user->id === $produk->user_id;
    }

    /**
     * Determine if user can update the product
     */
    public function update(User $user, ProdukSiapJual $produk): bool
    {
        return $user->id === $produk->user_id;
    }

    /**
     * Determine if user can delete the product
     */
    public function delete(User $user, ProdukSiapJual $produk): bool
    {
        return $user->id === $produk->user_id;
    }
}
