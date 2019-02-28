<?php

namespace Wearesho\Phonet\Yii;

/**
 * Interface IdentityInterface
 * @package Wearesho\Phonet\Yii
 */
interface IdentityInterface
{
    /**
     * @param string $number Phone number of the subscriber who calls you to the company or the subscriber your
     *     employee calls.
     * @param string|null $trunk phone number of the company to which the customer is calling (the field is set only
     *     for an incoming call).
     *
     * @return IdentityInterface|null Return null if customer is not exist in cms system
     */
    public static function findBy(string $number, ?string $trunk): ?IdentityInterface;

    /**
     * Customer name to which the number transferred belongs.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Reference to the opening of the client card that was found by phone number or creating a new one if the contact
     * was not found (it is advisable to add the phone parameter with the phone number, since a good phone number
     * should be installed in the card client when clicking on the notification).
     *
     * @return string
     */
    public function getUrl(): ?string;

    /**
     * link text to open a customer card. If a contact exists, it is best to set his name in this field; if the contact
     * does not exist, you can set the value to "Add contact".
     *
     * @return string
     */
    public function getUrlText(): ?string;

    /**
     * internal number of the responsible employee, is used to route the call to this employee in case of a regular
     * customer call.
     *
     * @return string|null
     */
    public function getResponsibleEmployeeExt(): ?string;

    /**
     * E-mail of the responsible employee, is used to route the call to this employee in case of a regular customer
     * call. This field is considered a higher priority and routing will occur through it if it is set.
     *
     * @return string|null
     */
    public function getResponsibleEmployeeEmail(): ?string;
}
