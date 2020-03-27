<?php

namespace Inspheric\Fields\Rules;

use Illuminate\Contracts\Validation\Rule;
use Inspheric\Fields\AddressRepository;

class AddressCountry implements Rule
{
    /**
     * Address repository.
     *
     * @var AddressRepository
     */
    protected $repository;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = app('address-field.repository');
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        dd($value);

        $country = $this->repository->country($value);

        dump($country);

        return !is_null($country);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.in', ['attribute' => $this->attribute]);
    }
}
