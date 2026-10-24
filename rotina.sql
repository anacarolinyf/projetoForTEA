-- Rode no phpMyAdmin (banco fortea), aba SQL
CREATE TABLE rotinas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  nome VARCHAR(80) NOT NULL,
  criada_em DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE atividades_padrao (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(60) NOT NULL,
  categoria VARCHAR(30) NOT NULL,
  emoji VARCHAR(8) NOT NULL
);
CREATE TABLE rotina_itens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rotina_id INT NOT NULL,
  titulo VARCHAR(60) NOT NULL,
  emoji VARCHAR(8) NULL,
  imagem VARCHAR(80) NULL,
  horario TIME NULL,
  ordem INT NOT NULL DEFAULT 0,
  FOREIGN KEY (rotina_id) REFERENCES rotinas(id) ON DELETE CASCADE
);
-- existir uma linha aqui = "feito" naquele dia (zera sozinho no dia seguinte)
CREATE TABLE rotina_progresso (
  item_id INT NOT NULL,
  dia DATE NOT NULL,
  PRIMARY KEY (item_id, dia),
  FOREIGN KEY (item_id) REFERENCES rotina_itens(id) ON DELETE CASCADE
);
INSERT INTO atividades_padrao (nome, categoria, emoji) VALUES
('Acordar','Manhã','⏰'),('Escovar os dentes','Higiene','🪥'),('Tomar banho','Higiene','🚿'),
('Trocar de roupa','Higiene','👕'),('Ir ao banheiro','Higiene','🚽'),('Café da manhã','Alimentação','🥣'),
('Almoço','Alimentação','🍽️'),('Lanche','Alimentação','🍎'),('Jantar','Alimentação','🍝'),
('Ir para a escola','Escola','🎒'),('Lição de casa','Escola','✏️'),('Terapia','Saúde','🧩'),
('Brincar','Lazer','🧸'),('Tela / TV','Lazer','📺'),('Passeio','Lazer','🌳'),('Dormir','Noite','🌙');
