<?php

namespace PlacetoPay\BancolombiaSDK\Entities;

class TransferResponse
{
    protected $code;
    protected $state;
    protected $description;
    protected $authorization;
    protected $date;
    protected $reference;
    protected $amount;
    /**
     * @var array
     */
    protected $meta;

    const ST_APPROVED = 'approved';
    const ST_PENDING = 'pending';
    const ST_REJECTED = 'rejected';

    public function __construct(array $data)
    {
        $this->meta = $data['meta'] ?? [];

        $this->code = $data['code'];
        $this->state = $data['state'];
        $this->description = $data['description'];
        $this->authorization = $data['authorization'];
        $this->date = $data['date'];
        $this->reference = $data['reference'];
        $this->amount = $data['amount'];
    }

    /**
     * The queried code used.
     * @return string
     */
    public function code(): string
    {
        return $this->code;
    }

    public function state()
    {
        return $this->state;
    }

    public function description()
    {
        return $this->description;
    }

    public function authorization()
    {
        return $this->authorization;
    }

    /**
     * Returns the date on GMT-05:00.
     * @return null|string
     */
    public function date(): ?string
    {
        if (!$this->date) {
            return null;
        }
        return (new \DateTime($this->date . '-05:00'))
            ->format('c');
    }

    public function reference()
    {
        return $this->reference;
    }

    public function amount()
    {
        return $this->amount;
    }

    public function meta(): array
    {
        return $this->meta;
    }

    public function isApproved(): bool
    {
        return $this->state() === self::ST_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->state() === self::ST_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->state() === self::ST_REJECTED;
    }

    public static function parseFromResponse(array $response): self
    {
        return new self([
            'meta' => $response['meta'],
            'code' => $response['data'][0]['header']['id'],
            'state' => $response['data'][0]['transferState'],
            'description' => $response['data'][0]['transferStateDescription'],
            'authorization' => $response['data'][0]['transferVoucher'],
            'date' => $response['data'][0]['transferDate'],
            'reference' => $response['data'][0]['transferReference'],
            'amount' => $response['data'][0]['transferAmount'],
        ]);
    }
}
