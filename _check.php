<?php
$db = new PDO('sqlite:database/portfolio.db');
// Check table structure
$cols = $db->query("PRAGMA table_info(experience_competence_blocks)")->fetchAll(PDO::FETCH_ASSOC);
echo "experience_competence_blocks columns:\n";
foreach ($cols as $c) echo "  " . $c['name'] . "\n";

$cols2 = $db->query("PRAGMA table_info(experience_sub_competences)")->fetchAll(PDO::FETCH_ASSOC);
echo "\nexperience_sub_competences columns:\n";
foreach ($cols2 as $c) echo "  " . $c['name'] . "\n";

echo "\nBlocks:\n";
$rows = $db->query("SELECT * FROM experience_competence_blocks WHERE experience_id=1")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) print_r($r);

echo "\nSub-competences:\n";
$rows = $db->query("SELECT * FROM experience_sub_competences WHERE experience_id=1")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) print_r($r);
<?php
$db = new PDO('sqlite:database/portfolio.db');
$rows = $db->query("SELECT eb.experience_id, cb.name as bloc, sc.name as sc_name, es.justification FROM experience_sub_competences es JOIN sub_competences sc ON es.sub_competence_id=sc.id JOIN experience_competence_blocks eb ON eb.experience_id=es.experience_id AND eb.block_id=sc.block_id JOIN competence_blocks cb ON cb.id=sc.block_id WHERE es.experience_id=1")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['bloc'] . " → " . $r['sc_name'] . "\n";
}
echo "\nTotal: " . count($rows) . " sous-compétences\n";
