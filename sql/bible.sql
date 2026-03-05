-- Listet die Text der Verse auf
SELECT text, t.name, c.number, v.id, v.number, b.name, b.abbreviation FROM `verse-text` 
INNER JOIN translation AS t ON translation_id = t.id
INNER JOIN verse AS v ON `verse-text`.`verse_id` = v.id
INNER JOIN chapter AS c ON v.chapter_id = c.id
INNER JOIN book AS b ON c.book_id = b.id
WHERE b.id = 4 AND c.id = 4 AND v.id = 3 AND t.id = 2
;

-- Listet die Verse eines Kapitels auf
SELECT text, t.name, c.number, v.number, b.name, b.abbreviation FROM `verse-text` 
INNER JOIN translation AS t ON translation_id = t.id
INNER JOIN verse AS v ON `verse-text`.`verse_id` = v.id
INNER JOIN chapter AS c ON v.chapter_id = c.id
INNER JOIN book AS b ON c.book_id = b.id
WHERE b.id = 4 AND c.id = 4 AND t.id = 1
ORDER BY v.number
;

§