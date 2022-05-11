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

namespace RM\Bundle\JwtSecurityBundle\Tests\Functional;

use Laminas\Math\Rand;
use RM\Bundle\JwtSecurityBundle\EventListener\KeyLoaderListener;
use RM\Bundle\JwtSecurityBundle\Security\JwtToken;
use RM\Standard\Jwt\Algorithm\Signature\RSA\RS512;
use RM\Standard\Jwt\Key\Parameter\Type;
use RM\Standard\Jwt\Key\Storage\KeyStorageInterface;
use RM\Standard\Jwt\Property\Payload\Subject;
use RM\Standard\Jwt\Serializer\SignatureSerializerInterface;
use RM\Standard\Jwt\Signature\SignatureToken;
use RM\Standard\Jwt\Signature\SignerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @covers \RM\Bundle\JwtSecurityBundle\Security\JwtAuthenticator
 *
 * @internal
 */
class JwtAuthenticatorTest extends TestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
        $container = $this->client->getContainer();

        /** @var KeyLoaderListener $keyLoader */
        $keyLoader = $container->get(KeyLoaderListener::class);
        $keyLoader->setEnabled(true);
        $keyLoader();
    }

    public function testValidToken(): void
    {
        $container = $this->client->getContainer();

        /** @var SignatureSerializerInterface $serializer */
        $serializer = $container->get(SignatureSerializerInterface::class);

        /** @var KeyStorageInterface $keyStorage */
        $keyStorage = $container->get(KeyStorageInterface::class);
        $key = $keyStorage->findByType(Type::RSA)[0];
        $rs512 = new RS512();

        $token = SignatureToken::createWithAlgorithm($rs512);
        $subjectId = Rand::getString(32);
        $token->getPayload()->set(new Subject($subjectId));

        /** @var SignerInterface $signer */
        $signer = $container->get(SignerInterface::class);

        $signed = $signer->sign($token, $rs512, $key);

        $serialized = $serializer->serialize($signed);
        $this->client->request(Request::METHOD_GET, '/secured', ['token' => $serialized]);
        self::assertResponseIsSuccessful();

        $tokenStorage = $container->get(TokenStorageInterface::class);
        $token = $tokenStorage->getToken();
        self::assertInstanceOf(JwtToken::class, $token);

        $subject = $token->getSubject();
        self::assertSame($token->getUser(), $subject);
        self::assertSame($subjectId, $subject->getIdentifier());
    }
}
