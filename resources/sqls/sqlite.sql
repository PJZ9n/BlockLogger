-- #! sqlite

-- #{ BlockLogger
    -- #{ blocklog
        -- #{ init
CREATE TABLE IF NOT EXISTS blocklog (
  id INTEGER NOT NULL PRIMARY KEY,
  action_type TEXT NOT NULL,
  player_name TEXT NOT NULL,
  x INTEGER NOT NULL,
  y INTEGER NOT NULL,
  z INTEGER NOT NULL,
  world TEXT NOT NULL,
  block_id INTEGER NOT NULL,
  block_meta INTEGER NOT NULL,
  block_name TEXT NOT NULL,
  block_itemid INTEGER NOT NULL,
  created_at NOT NULL DEFAULT (DATETIME('now', 'localtime'))
);
        -- #}
        -- #{ add
        -- #:action_type string
        -- #:player_name string
        -- #:x int
        -- #:y int
        -- #:z int
        -- #:world string
        -- #:block_id int
        -- #:block_meta int
        -- #:block_name string
        -- #:block_itemid int
INSERT INTO blocklog (
  action_type,
  player_name,
  x,
  y,
  z,
  world,
  block_id,
  block_meta,
  block_name,
  block_itemid
) VALUES (
  :action_type,
  :player_name,
  :x,
  :y,
  :z,
  :world,
  :block_id,
  :block_meta,
  :block_name,
  :block_itemid
);
        -- #}
        -- #{ get
            -- #{ bypos
            -- #:x int
            -- #:y int
            -- #:z int
            -- #:world string
            -- #:limit int
SELECT * FROM blocklog
WHERE x = :x
AND y = :y
AND z = :z
AND world = :world
ORDER BY id DESC
LIMIT :limit;
            -- #}
        -- #}
    -- #}
-- #}