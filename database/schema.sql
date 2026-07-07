CREATE DATABASE IF NOT EXISTS homecare_dialysis;
USE homecare_dialysis;

CREATE TABLE usuarios (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          nombre VARCHAR(100) NOT NULL,
                          usuario VARCHAR(50) NOT NULL UNIQUE,
                          contrasena VARCHAR(255) NOT NULL,
                          rol VARCHAR(20) NOT NULL DEFAULT 'paciente',
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pacientes (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           usuario_id INT,
                           nombre_completo VARCHAR(100) NOT NULL,
                           created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                           FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE medicos (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         usuario_id INT,
                         nombre_completo VARCHAR(100) NOT NULL,
                         especialidad VARCHAR(100),
                         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                         FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE medico_pacientes (
                                  id INT AUTO_INCREMENT PRIMARY KEY,
                                  medico_id INT,
                                  paciente_id INT,
                                  UNIQUE(medico_id, paciente_id),
                                  FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE CASCADE,
                                  FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
);

CREATE TABLE recambios (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           paciente_id INT,
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
                           created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                           FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
);

CREATE TABLE glicemias (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           paciente_id INT,
                           valor_glucosa DECIMAL(6,2) NOT NULL,
                           momento VARCHAR(30) NOT NULL,
                           diagnostico VARCHAR(30) NOT NULL,
                           created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                           FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
);

-- DATOS INICIALES
INSERT INTO usuarios (nombre, usuario, contrasena, rol)
VALUES
    ('Administrador', 'admin', '$2y$12$ODmfNDXaPV.uJMOrbWn6BO6PB/./9.GB/9Ideh6R3hhNFfxs5dPJG', 'admin'),
    ('Paciente Demo', 'paciente', '$2y$12$86SFNTiFOJcvDOgGG2dBjuyx/Z24n6x4UU3rkDNO2LfZxDmgpUmhi', 'paciente'),
    ('Doctor Demo', 'medico', '$2y$12$HdpHX83L/Daw22ymh3ymAu9A45jAWsXQjyv00pCJljQFi/6jIusUy', 'medico');

INSERT INTO pacientes (usuario_id, nombre_completo) VALUES (2, 'Paciente Demo');
INSERT INTO medicos (usuario_id, nombre_completo, especialidad) VALUES (3, 'Doctor Demo', 'Nefrología');
