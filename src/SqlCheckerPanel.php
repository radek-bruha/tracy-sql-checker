<?php declare(strict_types=1);

namespace Bruha\Tracy;

use Bruha\Tracy\Middleware\Logger;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;
use Tracy\IBarPanel;

final class SqlCheckerPanel implements IBarPanel
{

    private const SUCCESS = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAMAAAAMCGV4AAAAflBMVEUAAAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAAAiAASFJtHAAAAKXRSTlMAAQMEBQ0QERMXLC8wMTQ1OkNHTk9SWV9hYmdwkZilvMjP2t7v8/n7/Y4B51wAAABmSURBVAgdbcEHEoIwAEXBpyhiV7A3sBDy739BQ0JmGMddvPRK37Te0zN46wZropmkDeWczklOUX+GBBcFC7xsqeBIa/RMS3mHMc5E0bnCSayiF62dohV/3OVZ2xizBR6mserk/PoCRMkPd/99Fe4AAAAASUVORK5CYII=';
    private const ERROR   = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAMAAAAMCGV4AAAAflBMVEUAAAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD7v5q5AAAAKXRSTlMAAQMEBQ0QERMXLC8wMTQ1OkNHTk9SWV9hYmdwkZilvMjP2t7v8/n7/Y4B51wAAABmSURBVAgdbcEHEoIwAEXBpyhiV7A3sBDy739BQ0JmGMddvPRK37Te0zN46wZropmkDeWczklOUX+GBBcFC7xsqeBIa/RMS3mHMc5E0bnCSayiF62dohV/3OVZ2xizBR6mserk/PoCRMkPd/99Fe4AAAAASUVORK5CYII='; /* @phpstan-ignore-line */
    private const LABEL   = '<span title="SQL Checker"><img src="%s" style="padding-left: 2px; margin-top: 5px;"><span class="tracy-label">%s ms (%s)</span></span>';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Logger $logger,
    ) {
    }

    public function getTab(): string
    {
        $queries = $this->logger->getQueries();

        return sprintf(self::LABEL, self::SUCCESS, self::number($this->getDuration($queries), 1), count($queries));
    }

    public function getPanel(): string
    {
        $queries    = $this->logger->getQueries();
        $connection = $this->entityManager->getConnection();
        $connection->close();

        foreach ($queries as $query) {
            try {
                $rows = $connection->executeQuery(
                    $query->getExplainQuery(),
                    $query->getParameters(),
                )->iterateAssociative();

                foreach ($rows as $row) {
                    $query->addExplain(
                        new Explain(
                            (string) $row['table'],
                            (string) $row['type'],
                            explode(',', (string) $row['possible_keys']),
                            (string) $row['key'],
                            (int) $row['rows'],
                            (int) $row['filtered'],
                            (string) $row['Extra'],
                        ),
                    );
                }
            } catch (Throwable) {}
        }

        ob_start();

        $queries  = $queries;
        $duration = $this->getDuration($queries);

        include __DIR__ . '/SqlCheckerPanel.phtml';

        return (string) ob_get_clean();
    }

    public static function number(float|int $number, int $decimals = 0): string
    {
        return number_format($number, $decimals, '.', ' ');
    }

    /**
     * @param Query[] $queries
     */
    private function getDuration(array $queries): float
    {
        return array_sum(array_map(static fn(Query $query): float => $query->getDuration(), $queries));
    }

}
