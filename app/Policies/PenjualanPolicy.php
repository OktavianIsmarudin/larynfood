<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Penjualan;

class PenjualanPolicy
{
    /**
     * Determine if user can view the penjualan
     */
    public function view(User $user, Penjualan $penjualan): bool
    {
        return $user->id === $penjualan->user_id;
    }

    /**
     * Determine if user can update the penjualan
     */
    public function update(User $user, Penjualan $penjualan): bool
    {
        return $user->id === $penjualan->user_id;
    }

    /**
     * Determine if user can delete the penjualan
     */
    public function delete(User $user, Penjualan $penjualan): bool
    {
        return $user->id === $penjualan->user_id;
    }
}
