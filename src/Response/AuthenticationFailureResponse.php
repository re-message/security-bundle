<?php
/*
 * This file is a part of Re Message Security Bundle.
 * This package is a part of Re Message.
 *
 * @link      https://github.com/re-message/security-bundle
 * @link      https://dev.remessage.ru/packages/security-bundle
 * @copyright Copyright (c) 2018-2022 Re Message
 * @author    Oleg Kozlov <h1karo@remessage.ru>
 * @license   Apache License 2.0
 * @license   https://legal.remessage.ru/licenses/security-bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RM\Bundle\JwtSecurityBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

final class AuthenticationFailureResponse extends JsonResponse
{
    private string $message;

    public function __construct(string $message = 'Bad credentials', int $status = self::HTTP_UNAUTHORIZED)
    {
        $this->message = $message;

        parent::__construct(null, $status);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        $this->setData();

        return $this;
    }

    /**
     * Sets the response data with the statusCode & message included.
     *
     * @inheritDoc
     */
    public function setData(mixed $data = []): static
    {
        return parent::setData((array) $data + ['code' => $this->statusCode, 'message' => $this->message]);
    }
}
