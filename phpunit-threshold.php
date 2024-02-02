<?php

$file = "./.coverage/index.xml";
$coverage = simplexml_load_file($file);
$coverageIsOk = true;
$errorMessage = "";
$totals = [
    [
        'name' => "Lines",
        'threshold' => 51,
        'ratio' => (double)$coverage->project->directory->totals->lines["percent"]
    ],
    [
        'name' => "Methods",
        'threshold' => 47,
        'ratio' => (double)$coverage->project->directory->totals->methods["percent"]
    ],
    [
        'name' => "Functions",
        'threshold' => 0,
        'ratio' => (double)$coverage->project->directory->totals->functions["percent"]
    ],
    [
        'name' => "Classes",
        'threshold' => 22,
        'ratio' => (double)$coverage->project->directory->totals->classes["percent"]
    ],
    [
        'name' => "Traits",
        'threshold' => 0,
        'ratio' => (double)$coverage->project->directory->totals->traits["percent"]
    ]
];

foreach ($totals as $total) {
    if ($total['ratio'] < $total['threshold']) {
        echo "{$total['name']} coverage failed! \r\n";
        echo "{$total['name']} coverage: {$total['ratio']}% \r\n";
        echo "Threshold: {$total['threshold']}% \r\n";
        $coverageIsOk = false;
        $errorMessage .= "{$total['name']} coverage failed! \r\n";
    } else {
        echo "{$total['name']} coverage success: {$total['ratio']}% \r\n";
    }
}

if (!$coverageIsOk) {
    echo "Coverage failed! \r\n";
    echo $errorMessage;
    exit(1);
} else {
    echo "Coverage success! \r\n";
}
