<?php
$info = json_decode(file_get_contents('./config.json'), true);

$assigneds = [];
function add($name, $date, $assignment, $class = null, $helper = null) {
    global $assigneds;
    if(empty($name)) return;
    $entry = implode(';', null_filter([$assignment, $class, $helper]));
    if(array_key_exists($name, $assigneds)) {
        if(array_key_exists($date, $assigneds[$name])) {
            if(!in_array($assignment, $assigneds[$name][$date], true)) {
                array_push($assigneds[$name][$date], $entry);
            }
        } else {
            $assigneds[$name][$date] = [$entry];
        }
    } else {
        $assigneds[$name] = [$date => [$entry]];
    }
}

function null_filter($array) {
    return array_filter($array, function($v) {
        return $v !== null;
    });
}

$dates = [];
foreach(glob('./data/*.{json}', GLOB_BRACE) as $file) {
    $contents = file_get_contents($file, true);
    $json = json_decode(utf8_encode($contents));
    foreach($json->meetings as $board) {
        if(!property_exists($board, 'message')) {
            $date = strtoupper($board->date);
            if(!in_array($date, $dates, true)) {
                array_push($dates, $date);
            }
            $board->date = array_search($date, $dates);
            add($board->chairman, $board->date, 'CH');
            add($board->spiritual_gems, $board->date, 'SG');
            add($board->closing_prayer, $board->date, 'CP');
            add($board->opening_talk->speaker, $board->date, 'OT');
            add($board->bible_reading->reader, $board->date, 'BR');
            if (isset($board->talk->student)) {
                add($board->talk->student, $board->date, 'TK');
            }
            if(isset($board->congregation_bible_study)) {
                add($board->congregation_bible_study->conductor, $board->date, 'CBS');
                if(isset($board->congregation_bible_study->reader)) {
                    add($board->congregation_bible_study->reader, $board->date, 'CBSR');
                }
            }
            foreach($board->living_as_christians as $living_as_christians) {
                add($living_as_christians->speaker, $board->date, 'LAC');
            }
            foreach(['initial_call', 'return_visit', 'bible_study'] as $assignment) {
                $student = $board->{$assignment}->student ?? null;
                $assistant = $board->{$assignment}->assistant ?? null;
                $assignment = ucwords(str_replace('_', ' ', $assignment));
                if ($student) {
                    add($student, $board->date, 'SA', $assignment, $assistant);
                }
                if ($assistant) {
                    add($assistant, $board->date, 'AA', $assignment, $student);
                }
            }
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" dir="ltr" lang="pt-br" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Waarom Explorer</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <link href="style.css" rel="stylesheet" />
    <style type="text/css">
        <?php foreach($info as $label => $data): ?>
            span.<?php print strtolower($label); ?> {
                background-color: <?php print $data['color'] ?>;
            }
        <?php endforeach; ?>
    </style>
</head>
<body>
    <div class="modal fade" id="filters" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filters</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php foreach($info as $label => $data): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="<?php print $label; ?>" value="<?php print $label; ?>" checked />
                        <label class="form-check-label" for="<?php print $label; ?>"><?php print $data['label']; ?> (<?php print $label; ?>)</label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="none">None</button>
                    <button type="button" class="btn btn-primary" id="all">All</button>
                </div>
            </div>
        </div>
    </div>
    <div id="waarom-assigner">
        <table class="table table-striped-columns table-hover m-0">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <?php foreach($dates as $date): ?>
                    <th scope="col"><?php print $date; ?></th>
                    <?php endforeach; ?>
                    <th scope="col" data-bs-toggle="tooltip" title="Total of meetings without assingment">#</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($assigneds as $name => $assignments): ?>
            <tr>
                <th scope="row"><i class="fa fa-minus-square" aria-hidden="true"></i><?php print $name; ?></th>
                <?php foreach($dates as $id => $column): ?>
                <td>
                <?php if(array_key_exists($id, $assignments)): foreach($assignments[$id] as $data): ?>
                    <?php @list($badge, $class, $helper) = explode(';', $data); ?>
                    <span <?php print $helper ? "helper=\"{$helper}\"" : null; ?>
                        data-bs-toggle="tooltip"
                        data-bs-html="true"
                        title="<?php print implode('<br />', null_filter([$info[$badge]['label'], $class, $helper])); ?>"
                        class="badge <?php print strtolower($badge); ?>"><?php print $badge; ?></span>
                <?php endforeach; endif; ?>
                </td>
                <?php endforeach; ?>
                <td>#</td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filters" id="draggable">Filters</button>
    <?php foreach(['draggable', 'filter', 'scroll', 'table', 'tooltip'] as $file): ?>
        <script src="scripts/<?php print $file ?>.js"></script>
    <?php endforeach; ?>
</body>
</html>
