<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pembelian;

class PembelianPolicy
{
    /**
     * Determine if user can view the pembelian
     */
    public function view(User $user, Pembelian $pembelian): bool
    {
        return $user->id === $pembelian->user_id;
    }

    /**
     * Determine if user can update the pembelian
     */
    public function update(User $user, Pembelian $pembelian): bool
    {
        return $user->id === $pembelian->user_id;
    }

    /**
     * Determine if user can delete the pembelian
     */
    public function delete(User $user, Pembelian $pembelian): bool
    {
        return $user->id === $pembelian->user_id;
    }
}
