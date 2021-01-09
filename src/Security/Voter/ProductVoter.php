<?php

namespace App\Security\Voter;

use App\Entity\CartItem;
use App\Entity\Customer;
use App\Entity\Producer;
use App\Entity\Product;
use Cassandra\Custom;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class ProductVoter
 * @package App\Security\Voter
 */
class ProductVoter extends Voter
{

    public const UPDATE = "update";

    public const DELETE = "delete";

    public const ADD_TO_CART = "add_to_cart";

    /**
     * @@inheritDoc
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::UPDATE, self::DELETE, self::ADD_TO_CART]) && $subject instanceof Product;
    }

    /**
     * @@inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Producer) {
            return false;
        }

        /** @var Product $subject */
        if ($attribute === self::ADD_TO_CART) {
            return $this->voteOnAddToCart($user, $subject);
        }

        return $subject->getFarm() === $user->getFarm();
    }

    private function voteOnAddToCart(Customer $customer, Product $product): bool
    {
        if ($customer->getCart()->count() === 0) {
            return true;
        }

        return $customer->getCart()
            ->map(fn(CartItem $cartItem) => $cartItem->getProduct()->getFarm())
            ->contains($product->getFarm());
    }
}
