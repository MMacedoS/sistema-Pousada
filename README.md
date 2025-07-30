# Sistema Reserva - Pousada

Este projeto é um sistema de reservas para pousadas, desenvolvido em **PHP puro** com foco em boas práticas de desenvolvimento, arquitetura limpa e princípios modernos de programação.

## Objetivo

Criar uma aplicação robusta, escalável e de fácil manutenção, utilizando **Clean Code**, **POO (Programação Orientada a Objetos)**, princípios **SOLID**, padrão **MVC (Model-View-Controller)**, com repositórios que implementam **Contracts** e utilizam **Entities**, além de recursos como **Service Container**, **Traits** e autenticação via **JWT**.

## Tecnologias e Bibliotecas Utilizadas

- **PHP (puro)**: Linguagem principal do projeto, sem frameworks, para maior controle e aprendizado dos conceitos fundamentais.
- **JWT (JSON Web Token)**: Para autenticação segura e stateless dos usuários.
- **Composer**: Gerenciamento de dependências e autoload.
- **Dotenv**: Gerenciamento de variáveis de ambiente.
- **PSR-4 Autoloading**: Organização e carregamento automático das classes.

## Arquitetura e Padrões

- **POO**: Todo o sistema é estruturado em classes, promovendo encapsulamento, reutilização e modularidade.
- **SOLID**: Os cinco princípios SOLID são aplicados para garantir código limpo, desacoplado e de fácil manutenção.
- **MVC (Model-View-Controller)**: Separação clara entre lógica de apresentação, controle e domínio.
- **Repositórios**: Camada responsável pelo acesso a dados, implementando **Contracts** (interfaces) para garantir desacoplamento e testabilidade, e utilizando **Entities** para representar os dados do domínio.
- **Service Container**: Gerenciamento centralizado de dependências e injeção de serviços.
- **Traits**: Compartilhamento de funcionalidades comuns entre classes sem herança múltipla.
- **JWT**: Implementação de autenticação baseada em tokens, aumentando a segurança da aplicação.

## Estrutura do Projeto

```
/sistemaReserva
├── app/
│   ├── Controllers/
│   ├── Config/
│   ├── Models/
│   ├── Repositories/
│   │   ├── Contracts/
│   │   └── ...
│   ├── Services/
│   ├── Traits/
│   └── Views/
├── public/
│   └── index.php
├── vendor/
├── .env
├── composer.json
└── README.md
```

## Principais Funcionalidades

- Cadastro, autenticação e gerenciamento de usuários (JWT)
- Cadastro e gerenciamento de reservas
- Gerenciamento de quartos e disponibilidade
- Interface limpa e intuitiva

## Como Executar

1. Clone o repositório:
    ```bash
    git clone https://github.com/MMacedoS/sistemaReserva.git
    ```
2. Instale as dependências:
    ```bash
    composer install
    ```
3. Configure o arquivo `.env` com as variáveis necessárias.
4. Inicie o servidor embutido do PHP:
    ```bash
    php -S localhost:8000 -t public
    ```

## Contribuição

Sinta-se à vontade para abrir issues ou enviar pull requests com melhorias e correções.

---

Desenvolvido com foco em qualidade, organização e boas práticas de desenvolvimento PHP.
