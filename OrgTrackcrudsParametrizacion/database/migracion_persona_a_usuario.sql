-- ===============================
-- SCRIPT DE MIGRACIÓN
-- De estructura antigua a nueva (campos persona en usuarios)
-- ===============================

-- PASO 1: Agregar columnas de persona a tabla usuarios
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS nombre VARCHAR(100);
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS apellido VARCHAR(100);
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS ci VARCHAR(20);
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS telefono VARCHAR(20);

-- PASO 2: Migrar datos de persona a usuarios (si existe la tabla persona)
DO $$ 
BEGIN
    IF EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'persona') THEN
        UPDATE usuarios u
        SET 
            nombre = p.nombre,
            apellido = p.apellido,
            ci = p.ci,
            telefono = p.telefono
        FROM persona p
        WHERE u.id_persona = p.id;
    END IF;
END $$;

-- PASO 3: Hacer NOT NULL los campos migrados (después de migrar datos)
ALTER TABLE usuarios ALTER COLUMN nombre SET NOT NULL;
ALTER TABLE usuarios ALTER COLUMN apellido SET NOT NULL;
ALTER TABLE usuarios ALTER COLUMN ci SET NOT NULL;

-- PASO 4: Agregar constraint UNIQUE a CI
ALTER TABLE usuarios ADD CONSTRAINT usuarios_ci_unique UNIQUE (ci);

-- PASO 5: Eliminar la columna id_persona y constraint de usuarios
ALTER TABLE usuarios DROP CONSTRAINT IF EXISTS fk_usuarios_persona;
ALTER TABLE usuarios DROP COLUMN IF EXISTS id_persona;

-- PASO 6: Eliminar la tabla persona (si existe)
DROP TABLE IF EXISTS persona CASCADE;

-- INFORMACIÓN
SELECT 'Migración completada. Campos de persona ahora están en tabla usuarios.' as mensaje;
