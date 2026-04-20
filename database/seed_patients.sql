BEGIN TRANSACTION;

-- Insertar Pacientes
INSERT INTO patients (doctor_id, name, age, identity_card, occupation, phone, created_at, updated_at) VALUES 
(3, 'Carmen Rodriguez', 45, '12555666', 'Comerciante', '04120001111', datetime('now'), datetime('now')),
(3, 'Elena Martinez', 23, '28111222', 'Estudiante', '04243334444', datetime('now'), datetime('now')),
(3, 'Lucia Fernandez', 31, '22333444', 'Arquitecta', '04165556666', datetime('now'), datetime('now')),
(3, 'Beatriz Torres', 52, '11444555', 'Docente', '04127778888', datetime('now'), datetime('now')),
(3, 'Sofia Vargas', 29, '24555666', 'Medico', '04249990000', datetime('now'), datetime('now')),
(3, 'Rosa Mendez', 60, '8123456', 'Jubilada', '04141112222', datetime('now'), datetime('now')),
(3, 'Gabriela Castro', 37, '19222333', 'Contadora', '04123334444', datetime('now'), datetime('now')),
(3, 'Irene Rojas', 41, '15444555', 'Estilista', '04245556666', datetime('now'), datetime('now')),
(3, 'Clara Suarez', 26, '26555666', 'Chef', '04167778888', datetime('now'), datetime('now')),
(3, 'Victoria Lara', 33, '21666777', 'Abogada', '04129990000', datetime('now'), datetime('now')),
(3, 'Olga Peña', 48, '14777888', 'Administradora', '04241113333', datetime('now'), datetime('now')),
(3, 'Julia Blanco', 30, '23888999', 'Diseñadora', '04123335555', datetime('now'), datetime('now')),
(3, 'Martha Ruiz', 55, '99111222', 'Ama de Casa', '04165557777', datetime('now'), datetime('now')),
(3, 'Diana Cruz', 27, '25111222', 'Enfermera', '04247779999', datetime('now'), datetime('now')),
(3, 'Paola Silva', 32, '22222333', 'Psicologa', '04121114444', datetime('now'), datetime('now')),
(3, 'Andrea Ortiz', 24, '27333444', 'Marketing', '04243336666', datetime('now'), datetime('now')),
(3, 'Silvia Gomez', 39, '17444555', 'Odontologa', '04165558888', datetime('now'), datetime('now')),
(3, 'Patricia Leal', 43, '13555666', 'Decoradora', '04127770000', datetime('now'), datetime('now')),
(3, 'Isabel Rios', 36, '19666777', 'Veterinaria', '04249992222', datetime('now'), datetime('now')),
(3, 'Natalia Mora', 25, '26777888', 'Periodista', '04161114444', datetime('now'), datetime('now'));

-- Insertar Historias Base para estos pacientes (usando ID por orden para simplificar si es base limpia, o por JOIN)
-- Para asegurar consistencia, creamos las historias basadas en las cédulas insertadas arriba
INSERT INTO base_histories (patient_id, menstrual_cycle, pregnancies_gpcav, allergies, personal_pathological, family_history, created_at, updated_at)
SELECT id, 'Regulares (28x4)', 'G:0 P:0 C:0 A:0 V:0', 'Ninguna conocida', 'Sana', 'Sin antecedentes de importancia', datetime('now'), datetime('now')
FROM patients 
WHERE identity_card IN ('12555666', '28111222', '22333444', '11444555', '24555666', '8123456', '19222333', '15444555', '26555666', '21666777', '14777888', '23888999', '99111222', '25111222', '22222333', '27333444', '17444555', '13555666', '19666777', '26777888');

COMMIT;
