<?php
/*
 * This file is a part of Re Message Security Bundle.
 * This package is a part of Re Message.
 *
 * @link      https://github.com/re-message/security-bundle
 * @link      https://dev.remessage.ru/packages/security-bundle
 * @copyright Copyright (c) 2018-2023 Re Message
 * @author    Oleg Kozlov <h1karo@remessage.ru>
 * @license   Apache License 2.0
 * @license   https://legal.remessage.ru/licenses/security-bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RM\Bundle\JwtSecurityBundle\Extractor;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Oleg Kozlov <h1karo@remessage.ru>
 */
class ChainTokenExtractor implements TokenExtractorInterface
{
    /**
     * @var Collection<int, TokenExtractorInterface>
     */
    private readonly Collection $extractors;

    public function __construct(array $extractors = [])
    {
        $this->extractors = new ArrayCollection();

        foreach ($extractors as $extractor) {
            $this->pushExtractor($extractor);
        }
    }

    public function pushExtractor(TokenExtractorInterface $extractor): void
    {
        $this->extractors->add($extractor);
    }

    public function extract(Request $request): ?string
    {
        return $this->findExtractor($request)?->extract($request);
    }

    public function supports(Request $request): bool
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($request)) {
                return true;
            }
        }

        return false;
    }

    public function getExtractors(): array
    {
        return $this->extractors->toArray();
    }

    private function findExtractor(Request $request): ?TokenExtractorInterface
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($request)) {
                return $extractor;
            }
        }

        return null;
    }
}
