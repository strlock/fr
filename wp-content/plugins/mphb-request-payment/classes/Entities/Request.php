<?php

namespace MPHB\Addons\RequestPayment\Entities;

/**
 * @since 2.0.0
 */
class Request
{
    const REQUEST_TYPE_FULL     = 'full';
    const REQUEST_TYPE_DEPOSIT  = 'deposit';
    const REQUEST_TYPE_PERCENT  = 'percent';
    const REQUEST_TYPE_FIXED    = 'fixed';

    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var string
     */
    protected $type = self::REQUEST_TYPE_FULL;

    /**
     * @var int|float
     */
    protected $amount = 0;

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @param int $id Optional. 0 by default.
     * @param array $data Optional.
     */
    public function __construct($id = 0, $data = array())
    {
        $this->id = $id;
        $this->setupFields($data);
    }

    /**
     * @param array $data
     */
    protected function setupFields($data)
    {
        foreach (['type', 'amount', 'description'] as $field) {
            if (array_key_exists($field, $data)) {
                $this->$field = $data[$field];
            }
        }
    }

    /**
     * @param 'all'|'no-id' $fields Optional. 'all' by default.
     * @return array ['type', 'amount', 'description']
     */
    public function toArray($fields = 'all')
    {
        $data = array(
            'type'        => $this->type,
            'amount'      => $this->amount,
            'description' => $this->description,
        );

        if ($fields != 'no-id') {
            $data = array_merge(array('id' => $this->id), $data);
        }

        return $data;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int|float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return boolean
     */
    public function hasDescription()
    {
        return !empty($this->description);
    }

    /**
     * @return boolean
     */
    public function isCustomRequest()
    {
        return $this->type == self::REQUEST_TYPE_PERCENT
            || $this->type == self::REQUEST_TYPE_FIXED;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param int|float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
