<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StockGudang;

class StockGudangPolicy
{
    /**
     * Determine if user can view the stock
     */
    public function view(User $user, StockGudang $stockGudang): bool
    {
        return $user->id === $stockGudang->user_id;
    }

    /**
     * Determine if user can update the stock
     */
    public function update(User $user, StockGudang $stockGudang): bool
    {
        return $user->id === $stockGudang->user_id;
    }

    /**
     * Determine if user can delete the stock
     */
    public function delete(User $user, StockGudang $stockGudang): bool
    {
        return $user->id === $stockGudang->user_id;
    }
}
