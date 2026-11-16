-- Banco do Gerenciador Logistico
-- Importar pelo phpMyAdmin do XAMPP (aba Importar) ou rodar direto na aba SQL

CREATE DATABASE IF NOT EXISTS gerenciador_logistico
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;

USE gerenciador_logistico;

CREATE TABLE produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  descricao VARCHAR(255),
  peso_kg DECIMAL(10,2) NOT NULL DEFAULT 0,
  largura_cm DECIMAL(10,2) DEFAULT 0,
  altura_cm DECIMAL(10,2) DEFAULT 0,
  comprimento_cm DECIMAL(10,2) DEFAULT 0,
  valor DECIMAL(10,2) NOT NULL DEFAULT 0,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- o mesmo endereco pode ser usado como origem em um frete e destino em outro
CREATE TABLE enderecos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  apelido VARCHAR(60) NOT NULL,
  cep VARCHAR(9) NOT NULL,
  logradouro VARCHAR(120) NOT NULL,
  numero VARCHAR(10) NOT NULL,
  complemento VARCHAR(60),
  bairro VARCHAR(80) NOT NULL,
  cidade VARCHAR(80) NOT NULL,
  uf CHAR(2) NOT NULL,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transportadores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  cpf_cnpj VARCHAR(18) NOT NULL,
  telefone VARCHAR(15),
  email VARCHAR(100),
  veiculo VARCHAR(60) NOT NULL,
  placa VARCHAR(8) NOT NULL,
  capacidade_kg DECIMAL(10,2) NOT NULL DEFAULT 0,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE fretes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  transportador_id INT NOT NULL,
  origem_id INT NOT NULL,
  destino_id INT NOT NULL,
  data_frete DATE NOT NULL,
  valor_frete DECIMAL(10,2) NOT NULL DEFAULT 0,
  status VARCHAR(20) NOT NULL DEFAULT 'Agendado',
  observacao VARCHAR(255),
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (transportador_id) REFERENCES transportadores(id),
  FOREIGN KEY (origem_id) REFERENCES enderecos(id),
  FOREIGN KEY (destino_id) REFERENCES enderecos(id)
);

-- tabela que faz a ligacao 1 frete (1 transportador) -> varios produtos
CREATE TABLE frete_produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  frete_id INT NOT NULL,
  produto_id INT NOT NULL,
  quantidade INT NOT NULL DEFAULT 1,
  FOREIGN KEY (frete_id) REFERENCES fretes(id) ON DELETE CASCADE,
  FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- alguns dados pra testar
INSERT INTO produtos (nome, descricao, peso_kg, largura_cm, altura_cm, comprimento_cm, valor) VALUES
('Saco de ração 25kg', 'Ração bovina engorda', 25, 40, 15, 70, 129.90),
('Caixa de ferramentas', 'Kit com 110 peças', 8.5, 45, 20, 30, 349.00),
('Rolo de arame liso', 'Arame 1000m', 22, 50, 50, 20, 410.50);

INSERT INTO enderecos (apelido, cep, logradouro, numero, complemento, bairro, cidade, uf) VALUES
('Depósito Central', '87501-000', 'Avenida Paraná', '1500', 'Barracão 2', 'Centro', 'Umuarama', 'PR'),
('Cliente Fazenda Boa Vista', '87700-000', 'Estrada Rural PR-323', 'S/N', 'Km 12', 'Zona Rural', 'Paranavaí', 'PR');

INSERT INTO transportadores (nome, cpf_cnpj, telefone, email, veiculo, placa, capacidade_kg) VALUES
('Transportes Silva LTDA', '12.345.678/0001-90', '(44) 99999-1234', 'contato@transilva.com.br', 'Caminhão 3/4', 'ABC1D23', 4000);
