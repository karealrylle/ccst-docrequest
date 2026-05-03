<?php
$c = file_get_contents("resources/views/student/requests/show.blade.php");

$replacement = "font-size: 0.85rem;
        padding: 10px 28px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        letter-spacing: 0.3px;
        transition: background 0.2s;
        font-family: 'Poppins', sans-serif;
    }
    .btn-cancel:hover { background: #0D7FBF; color: white; }
</style>

<script>
/* ── Method Pill Selection ─────────────────────────────────────────\n";

$c = preg_replace("/font-siz[^\n]*?\n/s", $replacement, $c);

file_put_contents("resources/views/student/requests/show.blade.php", $c);
echo 'done';
