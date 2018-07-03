<?php

namespace Jikan\Parser\Anime;

use Jikan\Helper\Parser;
use Jikan\Model\EpisodeListItem;
use Jikan\Model\VoiceActor;
use Jikan\Parser\Episode\VoiceActorParser;
use Jikan\Parser\ParserInterface;
use Symfony\Component\DomCrawler\Crawler;
use Jikan\Model\DateRange;

/**
 * Class EpisodeListItemParser
 *
 * @package Jikan\Parser\Episode
 */
class EpisodeListItemParser implements ParserInterface
{
    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * EpisodeListItemParser constructor.
     *
     * @param Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * @return EpisodeListItem
     */
    public function getModel(): EpisodeListItem
    {
        return EpisodeListItem::fromParser($this);
    }

    /**
     * @return int
     */
    public function getEpisodeId(): int
    {
        return (int) $this->crawler->filterXPath('//td[contains(@class, \'episode-number\')]')->text();
    }

    /**
     * @return string
     */
    public function getEpisodeUrl(): string
    {
        return $this->crawler->filterXPath('//td[@class="episode-title"]/a')->attr('href');
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->crawler->filterXPath('//td[@class="episode-title"]/a')->text();
    }

    /**
     * @return ?string
     */
    public function getTitleJapanese(): ?string
    {
        $title = $this->crawler->filterXPath('//td[@class="episode-title"]/span[@class=\'di-ib\']')->text();

        if (empty($title)) {
            return null;
        }

        preg_match('~(.*)\((.*)\)~', $title, $matches);

        return $matches[2];
    }

    /**
     * @return ?string
     */
    public function getTitleRomanji(): ?string
    {
        $title = $this->crawler->filterXPath('//td[@class="episode-title"]/span[@class=\'di-ib\']')->text();

        if (empty($title)) {
            return null;
        }

        preg_match('~(.*)\((.*)\)~', $title, $matches);

        return (!empty($matches[1]) ? $matches[1] : null);
    }

    /**
     * @return ?DateRange
     */
    public function getAired(): ?DateRange
    {
        $aired = $this->crawler->filterXPath('//td[contains(@class, \'episode-aired\')]')->text();

        if ($aired === 'N/A') {
            return null;
        }

        return new DateRange($aired);
    }

    /**
     * @return bool
     */
    public function getFiller(): bool
    {
        $filler = $this->crawler->filterXPath(
            '//td
            [
                @class="episode-title"]
                /span[contains(@class, \'icon-episode-type-bg\') and contains(text(), \'Filler\')
            ]'
        );

        if (!$filler->count()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function getRecap(): bool
    {
        $recap = $this->crawler->filterXPath(
            '//td
            [
                @class="episode-title"]
                /span[contains(@class, \'icon-episode-type-bg\') and contains(text(), \'Recap\')
            ]'
        );

        if (!$recap->count()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getVideoUrl(): ?string
    {
        $video = $this->crawler->filterXPath('//td[contains(@class, \'episode-video\')]/a');

        if (!$video->count()) {
            return null;
        }

        return $video->attr('href');
    }

    /**
     * @return string
     */
    public function getForumUrl(): ?string
    {
        $forum = $this->crawler->filterXPath('//td[contains(@class, \'episode-forum\')]/a');

        if (!$forum->count()) {
            return null;
        }

        return $forum->attr('href');
    }
}