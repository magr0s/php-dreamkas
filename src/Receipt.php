<?php

namespace DevGroup\Dreamkas;
use DevGroup\Dreamkas\exceptions\ValidationException;


/**
 * Class Receipt
 */
class Receipt extends Configurable
{
    public const TYPE_SALE = 'SALE';
    public const TYPE_REFUND = 'REFUND';
    public const TYPE_OUTFLOW = 'OUTFLOW';
    public const TYPE_OUTFLOW_REFUND = 'OUTFLOW_REFUND';

    public $type = self::TYPE_SALE;

    public $timeout = 300;

    public $taxMode;

    /** @var Position[] */
    public $positions = [];

    /** @var Payment[] */
    public $payments = [];

    /** @var CustomerAttributes|array */
    public $attributes = [];

    public $total = [
        'priceSum' => 0,
    ];


    /**
     * @return array
     */
    public function toArray(): array
    {
        $this->calculateSum();
        return parent::toArray(); // TODO: Change the autogenerated stub
    }

    /**
     * Calculates sum of receipt
     */
    public function calculateSum()
    {
        $sum = 0;
        foreach ($this->positions as $position) {
            $position->calculate();
            $sum += $position->priceSum;
        }

        $this->total = ['priceSum' => $sum];
    }

    /**
     * @return bool
     * @throws ValidationException
     */
    public function validate(): bool
    {
        if (\count($this->payments) === 0) {
            throw new ValidationException('No payments specified');
        }

        if (\count($this->positions) === 0) {
            throw new ValidationException('No positions specified');
        }

        if ($this->taxMode === null) {
            throw new ValidationException('No taxMode specified');
        }

        if (is_array($this->attributes) && \count($this->attributes) === 0) {
            throw new ValidationException('No customer attributes specified');
        }
        if ($this->attributes instanceof CustomerAttributes) {
            $this->attributes->validate();
        }

        foreach ($this->positions as $position) {
            $position->validate();
        }

        return true;
    }
}