<?php
/*
 *
 * This file is part of the Kiwicore package.
 *
 * (c) Simcao EI <dev@simcao.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  2023
 */


namespace App\Service;

/**
 * Parse data for ChartJs
 *
 * @author Simcao EI
 */
class ChartDataParser
{
    /**
     * @var array
     */
    private array $labels;

    /**
     * @var array
     */
    private array $datasets;

    /**
     * @param array $labels
     * @return $this
     */
    public function setLabels(array $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @param string $label
     * @param array $data
     * @return $this
     */
    public function setDataset(string $label, array $data): self
    {
        $this->datasets[] = [
            'label' => $label,
            'data' => $data
        ];

        return $this;
    }

    /**
     * @return array
     */
    public function getParsedData(): array
    {
        return [
            'labels' => $this->labels,
            'datasets' => $this->datasets
        ];
    }
}