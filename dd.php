<?php


function dd(...$args): void
{
    echo '<style>
        pre.dd-container {
            background: #18171B;
            padding: 1rem;
            border-radius: 6px;
            margin: 1rem 0;
            font-family: "JetBrains Mono", "Fira Code", monospace;
            font-size: 13px;
            line-height: 1.6;
            overflow-x: auto;
            color: #F8F8F2; /* Default text color (brackets, arrows) */
        }
        .dd-string { color: #7be57c; } /* Greenish-yellow for strings */
        .dd-number { color: #AE81FF; } /* Purple for numbers */
        .dd-boolean { color: #AE81FF; } /* Purple for booleans */
        .dd-null { color: #66D9EF; font-style: italic; } /* Cyan for null */
        .dd-key { color: #66D9EF; } /* Cyan for array/object keys */
        .dd-object-class { color: #F8F8F2; font-weight: bold; } /* White and bold for class names */
        .dd-object-id { color: #FF6188; } /* Pink/Red for object ID */
        .dd-recursion { color: #FD971F; font-weight: bold; } /* Orange for recursion notice */
    </style>';

    foreach ($args as $arg) {
        $visited = [];
        echo '<pre class="dd-container">' . format_variable($arg, 0, $visited) . '</pre>';
    }
    exit(1);
}

function format_variable($variable, int $indentationLevel = 0, array &$visited = []): string
{
    $indent = str_repeat('  ', $indentationLevel);

    switch (gettype($variable)) {
        case 'string':
            return $indent . '<span class="dd-string">"' . htmlspecialchars($variable) . '"</span>';

        case 'integer':
        case 'double':
            return $indent . '<span class="dd-number">' . $variable . '</span>';

        case 'boolean':
            return $indent . '<span class="dd-boolean">' . ($variable ? 'true' : 'false') . '</span>';

        case 'NULL':
            return $indent . '<span class="dd-null">null</span>';

        case 'array':
            $arrayId = spl_object_hash((object) $variable);
            if (isset($visited[$arrayId])) {
                return $indent . '<span class="dd-recursion">RECURSION</span>';
            }
            $visited[$arrayId] = true;

            if (empty($variable)) {
                return $indent . '[]';
            }

            $output = $indent . '[<br>';
            foreach ($variable as $key => $value) {
                $keyDisplay = is_string($key) ? '"' . htmlspecialchars($key) . '"' : $key;
                $output .= $indent . '  <span class="dd-key">' . $keyDisplay . '</span> => ' . format_variable($value, $indentationLevel + 1, $visited) . '<br>';
            }

            unset($visited[$arrayId]);

            return $output . $indent . ']';

        case 'object':
            $objectId = spl_object_id($variable);
            if (isset($visited[$objectId])) {
                return $indent . '<span class="dd-object-class">' . get_class($variable) . '</span> <span class="dd-recursion">{RECURSION}</span>';
            }
            $visited[$objectId] = true;

            $className = get_class($variable);
            $properties = get_object_vars($variable);
            $output = $indent . '<span class="dd-object-class">' . $className . '</span> <span class="dd-object-id">{#' . $objectId . '}</span> {<br>';

            if (empty($properties)) {
                $output .= $indent . '  <span style="color: #75715E;">// (no public properties)</span><br>';
            } else {
                foreach ($properties as $key => $value) {
                    $output .= $indent . '  <span class="dd-key">"' . htmlspecialchars($key) . '"</span> => ' . format_variable($value, $indentationLevel + 1, $visited) . '<br>';
                }
            }

            unset($visited[$objectId]);

            return $output . $indent . '}';

        default:
            return $indent . htmlspecialchars(print_r($variable, true));
    }
}
