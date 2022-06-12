<?php declare(strict_types=1);

namespace Bruha\Tracy;

use Doctrine\DBAL\Logging\LoggerChain;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;
use Tracy\IBarPanel;

/**
 * Class SqlCheckerPanel
 *
 * @package Bruha\Tracy
 */
final class SqlCheckerPanel implements IBarPanel
{

    private const SUCCESS = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAMAAAAMCGV4AAAAflBMVEUAAAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAASFJtHAAAAKXRSTlMAAQMEBQ0QERMXLC8wMTQ1OkNHTk9SWV9hYmdwkZilvMjP2t7v8/n7/Y4B51wAAABmSURBVAgdbcEHEoIwAEXBpyhiV7A3sBDy739BQ0JmGMddvPRK37Te0zN46wZropmkDeWczklOUX+GBBcFC7xsqeBIa/RMS3mHMc5E0bnCSayiF62dohV/3OVZ2xizBR6mserk/PoCRMkPd/99Fe4AAAAASUVORK5CYII=';
    private const ERROR   = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAMAAAAMCGV4AAAAflBMVEUAAAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD7v5q5AAAAKXRSTlMAAQMEBQ0QERMXLC8wMTQ1OkNHTk9SWV9hYmdwkZilvMjP2t7v8/n7/Y4B51wAAABmSURBVAgdbcEHEoIwAEXBpyhiV7A3sBDy739BQ0JmGMddvPRK37Te0zN46wZropmkDeWczklOUX+GBBcFC7xsqeBIa/RMS3mHMc5E0bnCSayiF62dohV/3OVZ2xizBR6mserk/PoCRMkPd/99Fe4AAAAASUVORK5CYII=';
    private const LABEL   = '<span title="SQL Checker"><img src="%s" style="padding-left: 2px; margin-top: 5px;"><span class="tracy-label">%s ms (%s)</span></span>';

    /**
     * @var SqlLogger
     */
    private SqlLogger $sqlLogger;

    /**
     * SqlCheckerPanel constructor
     *
     * @param EntityManagerInterface $entityManager
     * @param int                    $threshold
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly int $threshold = 100
    ) {
        $configuration   = $this->entityManager->getConfiguration();
        $sqlLogger       = $configuration->getSQLLogger();
        $this->sqlLogger = new SqlLogger();

        $configuration->setSQLLogger(new LoggerChain(array_merge($sqlLogger ? [$sqlLogger] : [], [$this->sqlLogger])));
    }

    /**
     * @return string
     */
    public function getTab(): string
    {
        $queries  = $this->sqlLogger->getQueries();
        $duration = array_sum(array_map(static fn(Query $query): float => $query->getDuration(), $queries)) * 1_000;

        return sprintf(
            self::LABEL,
            $duration >= ($this->threshold * count($queries)) ? self::ERROR : self::SUCCESS,
            self::number($duration, 1),
            count($queries)
        );
    }

    /**
     * @return string
     */
    public function getPanel(): string
    {
        $queries    = $this->sqlLogger->getQueries();
        $connection = $this->entityManager->getConnection();
        $connection->close();

        foreach ($queries as $query) {
            try {
                $query->setExplain(
                    array_map(
                        fn(array $explain): Explain => new Explain(
                            (string) $explain['table'],
                            (string) $explain['type'],
                            (string) $explain['possible_keys'],
                            (string) $explain['key'],
                            (int) $explain['rows'],
                            (int) $explain['filtered'],
                            (string) $explain['Extra'],
                        ),
                        iterator_to_array($connection->executeQuery(sprintf('EXPLAIN EXTENDED %s', $query->getQuery()))->iterateAssociativeIndexed())
                    )
                );
            } catch (Throwable) {

            }
        }

        ob_start();

        $queries = $queries;

        include __DIR__ . '/SqlCheckerPanel.phtml';

        return (string) ob_get_clean();
    }

    /**
     * @param float|int $number
     * @param int       $decimals
     *
     * @return string
     */
    public static function number(float|int $number, int $decimals = 0): string
    {
        return number_format($number, $decimals, '.', ' ');
    }

}
