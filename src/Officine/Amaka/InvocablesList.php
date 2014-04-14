<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka;

/**
 *
 *
 */
class InvocablesList extends \ArrayObject
{
    /**
     * Add the invocable object ~$invocable~ to the list
     *
     * @param Invocable $invocable
     */
    public function add(Invocable $invocable)
    {
        $this->offsetSet(self::elementKey($invocable), $invocable);
        return $this;
    }

    /**
     * Remove an element from the list
     *
     * @param Invocable $invocable
     */
    public function remove($invocable)
    {
        $this->offsetUnset(self::elementKey($invocable));
        return $this;
    }

    /**
     * Get an element from the list
     *
     * @param Invocable $invocable
     * @return Invocable
     */
    public function get($invocable)
    {
        if ($this->offsetExists(self::elementKey($invocable))) {
            return $this->offsetGet(self::elementKey($invocable));
        }
        return null;
    }

    /**
     * Check if an element is part of the list.
     *
     * @param Invocable $invocable
     * @return bool
     */
    public function contains($invocable)
    {
        return $this->offsetExists(self::elementKey($invocable));
    }

    /**
     * Return the first Invocable in the list
     *
     * @return Invocable
     */
    public function first()
    {
        $invs = $this->getArrayCopy();
        return reset($invs);
    }

    /**
     * Return the last Invocable in the list
     *
     * @return Invocable
     */
    public function last()
    {
        $invs = $this->getArrayCopy();
        return end($invs);
    }

    /**
     * If the list has no elements this method returns false.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return 0 == $this->count();
    }

    /**
     * Converts an element into its identifying name.
     *
     * The element need not be in the list to get its name.
     *
     * This function doesn't check if #$invocable# is contained in the
     * list, or if the string given is the name of an actual element
     * in the list.
     *
     * @param mixed $invocable
     * @return string
     */
    public static function elementKey($invocable)
    {
        if (is_string($invocable)) {
            return $invocable;
        }
        if ($invocable instanceof Invocable) {
            return $invocable->getName();
        }
        throw new \InvalidArgumentException("Empty invocable name or reference given.");
    }
}
