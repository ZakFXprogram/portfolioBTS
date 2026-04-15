<?php
$db = new PDO('sqlite:database/portfolio.db');
echo "sub_competences columns:\n";
$cols = $db->query("PRAGMA table_info(sub_competences)")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) echo "  " . $c['name'] . "\n";
echo "\nAll sub_competences:\n";
$rows = $db->query("SELECT * FROM sub_competences ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "sc_id {$r['id']}: {$r['name']}";
    if (isset($r['competence_block_id'])) echo " [block {$r['competence_block_id']}]";
    echo "\n";
}
<?php
$db = new PDO('sqlite:database/portfolio.db');
$rows = $db->query("SELECT sc.id, sc.name, cb.name as bloc FROM sub_competences sc JOIN competence_blocks cb ON cb.id = sc.block_id ORDER BY sc.block_id, sc.id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "sc_id {$r['id']}: [{$r['bloc']}] {$r['name']}\n";
}
