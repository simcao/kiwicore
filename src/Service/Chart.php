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
 * Service to create chart easily with chart.js
 *
 * To create a new chart :
 * $chart = new Chart();
 * $chart->setLabels(); | e.g. ['january', 'february' ...]
 * $chart->addDataset(); | e.g. 'Revenue', [100, 200 ...]
 * $chart = $chart->getChart();
 *
 * @author Simcao
 */
class Chart
{

    /**
     * @var string
     */
    private string $id;

    /**
     * @var array
     */
    private array $labels;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private ?string $dataset;

    public function __construct(string $type = 'bar')
    {
        $this->id = uniqid();
        $this->type = $type;
        $this->dataset = null;
    }

    public function getChart(): ?string
    {
        $code = $this->generateCanvas();
        $code .= $this->generateScripts();

        return $code;
    }

    public function setLabels(array $labels = []): ?self
    {
        $this->labels = $labels;

        return $this;
    }

    public function addDataset(string $label, array $data): self
    {
        $this->dataset .= '{';
        $this->dataset .= 'label: "' . $label . '",';
        $this->dataset .= 'data: [';
        foreach ($data as $item)
        {
            $this->dataset .= $item . ',';
        }
        $this->dataset .= '],';
        $this->dataset .= 'borderWidth: 1';
        $this->dataset .= '},';

        return $this;
    }

    private function generateCanvas(): ?string
    {
        return '<canvas id="' . $this->id . '"></canvas>';
    }

    private function generateScripts(): ?string
    {
        $code = '<script>';
        $code .= 'const ctx = document.getElementById("' . $this->id . '");';
        $code .= 'new Chart(ctx, {';
        $code .= 'type: "' . $this->type . '",';
        $code .= 'data: {';
        $code .= 'labels: [' . $this->formatLabels() . '],';
        $code .= 'datasets: [';

        $code .= $this->dataset;

        $code .= ']';
        $code .= '},';
        $code .= 'options: {';
        $code .= 'scales: {';
        $code .= 'y: {';
        $code .= 'beginAtZero: true';
        $code .= '}';
        $code .= '}';
        $code .= '}';
        $code .= '});';
        $code .= '</script>';


        return $code;
    }

    private function formatLabels(): ?string
    {
        $formattedLabels = null;

        foreach ($this->labels as $label)
        {
            $formattedLabels .= '"' . $label . '",';
        }

        return $formattedLabels;
    }

}

