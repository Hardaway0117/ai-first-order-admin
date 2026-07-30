<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Product $product): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Product $product): bool
    {
        // 員工可做日常維護（調庫存、改價、上下架）；新增與刪除仍限管理員
        return true;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }
}
