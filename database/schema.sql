
CREATE TABLE usuarios (
                          id SERIAL PRIMARY KEY,
                          nombre VARCHAR(100) NOT NULL,
                          usuario VARCHAR(50) NOT NULL UNIQUE,
                          contrasena VARCHAR(255) NOT NULL,
                          rol VARCHAR(20) NOT NULL DEFAULT 'paciente',
                          created_at TIMESTAMP DEFAULT NOW()
);


CREATE TABLE medicos (
                         id SERIAL PRIMARY KEY,
                         usuario_id INT REFERENCES usuarios(id) ON DELETE CASCADE,
                         nombre_completo VARCHAR(100) NOT NULL,
                         created_at TIMESTAMP DEFAULT NOW()
);


CREATE TABLE pacientes (
                           id SERIAL PRIMARY KEY,
                           usuario_id INT REFERENCES usuarios(id) ON DELETE CASCADE,
                           nombre_completo VARCHAR(100) NOT NULL,
                           created_at TIMESTAMP DEFAULT NOW()
);


CREATE TABLE medico_pacientes (
                                  id SERIAL PRIMARY KEY,
                                  medico_id INT REFERENCES medicos(id) ON DELETE CASCADE,
                                  paciente_id INT REFERENCES pacientes(id) ON DELETE CASCADE,
                                  UNIQUE(medico_id, paciente_id)
);

-- TABLA: recambios
CREATE TABLE recambios (
                           id SERIAL PRIMARY KEY,
                           paciente_id INT REFERENCES pacientes(id) ON DELETE CASCADE,
                           fecha_tratamiento DATE NOT NULL,
                           tipo_sistemadp VARCHAR(50) NOT NULL,
                           presion_arterial VARCHAR(20),
                           pulso INT,
                           recambio_num INT NOT NULL CHECK (recambio_num BETWEEN 1 AND 4),
                           concentracion VARCHAR(10) NOT NULL,
                           infusion INT NOT NULL DEFAULT 2000,
                           drenaje INT NOT NULL,
                           cualidad VARCHAR(20) NOT NULL,
                           balance INT NOT NULL,
                           created_at TIMESTAMP DEFAULT NOW()
);


CREATE TABLE glicemias (
                           id SERIAL PRIMARY KEY,
                           paciente_id INT REFERENCES pacientes(id) ON DELETE CASCADE,
                           valor_glucosa DECIMAL(6,2) NOT NULL,
                           momento VARCHAR(30) NOT NULL,
                           diagnostico VARCHAR(30) NOT NULL,
                           created_at TIMESTAMP DEFAULT NOW()
);

-- DATOS INICIALES
INSERT INTO usuarios (nombre, usuario, contrasena, rol)
VALUES --Todas sus contraseñas son "password"
    ('Administrador', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
    ('Paciente Demo', 'paciente', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'paciente'),
    ('Doctor Demo', 'medico', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'medico');

INSERT INTO pacientes (usuario_id, nombre_completo)
VALUES (2, 'Paciente Demo');

INSERT INTO medicos (usuario_id, nombre_completo, especialidad)
VALUES (3, 'Doctor Demo', 'Nefrología');