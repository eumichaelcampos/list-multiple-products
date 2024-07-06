### List Multiple Products Plugin

Este plugin permite listar múltiplos produtos em uma tabela através de um shortcode. Ele oferece diversas funcionalidades, como adicionar produtos individualmente ou em massa ao carrinho, resetar quantidades, e exibir informações detalhadas dos produtos.

## Funcionalidades

- Listar múltiplos produtos em uma tabela.
- Adicionar produtos individualmente ao carrinho.
- Adicionar todos os produtos ao carrinho de uma só vez.
- Resetar quantidade de produtos individualmente.
- Resetar todas as quantidades de produtos.
- Exibir notificações com a imagem do produto e uma mensagem de sucesso ao adicionar ao carrinho.
- Suporte para colunas personalizadas na tabela (Imagem, Produto, Categorias, Variações, Preço, Quantidade, Total, Adicionar ao Carrinho, Reset).

## Instalação

1. Baixe o plugin.
2. Extraia os arquivos no diretório `wp-content/plugins`.
3. Ative o plugin através do menu "Plugins" no WordPress.

## Uso

### Shortcode

O shortcode `[list-product]` é usado para exibir a tabela de produtos.

#### Parâmetros

- `products-id` (opcional): IDs dos produtos a serem listados, separados por vírgula.
- `id` (opcional): ID de uma lista de produtos previamente salva.
- `header` (opcional): `true` ou `false` (ativa ou desativa o cabeçalho da tabela).
- `options` (opcional): Colunas a serem exibidas na tabela, separadas por vírgula. Opções disponíveis: `Image,Product,Categories,Variations,Price,Quantity,Total,Add to Cart,Reset`.

#### Exemplo de Uso

```shortcode
[list-product products-id="375,377,366,361,346" header="true" options="Image,Product,Categories,Variations,Price,Quantity,Total,Add to Cart,Reset"]
```

## Personalização

### Alterar Colunas da Tabela

Você pode personalizar as colunas exibidas na tabela usando o parâmetro `options` no shortcode.

#### Exemplo

```shortcode
[list-product products-id="375,377,366,361,346" header="true" options="Image,Product,Price,Add to Cart"]
```

### Exibir ou Ocultar Cabeçalho

Use o parâmetro `header` para ativar ou desativar o cabeçalho da tabela.

#### Exemplo

```shortcode
[list-product products-id="375,377,366,361,346" header="false"]
```

### Notificações

O plugin exibe notificações ao adicionar produtos ao carrinho. As notificações incluem a imagem do produto e uma mensagem de sucesso.

### Personalização de Estilos

Você pode personalizar os estilos da tabela e das notificações editando os arquivos CSS localizados em `public/css/public-styles.css`.

### Personalização de Scripts

Os scripts JavaScript responsáveis por funcionalidades como adicionar ao carrinho e resetar quantidades estão localizados em `public/js/public-scripts.js`.

## Desenvolvimento

### Estrutura de Diretórios

```
list-multiple-products/
├── admin/
│   ├── css/
│   │   └── admin-styles.css
│   ├── js/
│   │   └── admin-scripts.js
│   └── class-lmp-admin.php
├── includes/
│   ├── class-lmp-settings.php
│   └── class-lmp-shortcode.php
├── languages/
│   └── list-multiple-products.pot
├── public/
│   ├── css/
│   │   └── public-styles.css
│   ├── js/
│   │   └── public-scripts.js
├── readme.txt
├── list-multiple-products.php
```

### Arquivo Principal

O arquivo principal `list-multiple-products.php` inicializa o plugin, carrega os scripts e estilos públicos, e define as funções AJAX para adicionar produtos ao carrinho.

### Configurações do Plugin

As configurações do plugin são gerenciadas pelo arquivo `class-lmp-settings.php` localizado na pasta `includes`.

### Shortcodes

O arquivo `class-lmp-shortcode.php` localizado na pasta `includes` define e renderiza o shortcode `[list-product]`.

### Administração

Os arquivos relacionados à administração do plugin, como os estilos e scripts do admin, estão localizados na pasta `admin`.

### Internacionalização

O plugin suporta tradução. Use o arquivo `.pot` localizado na pasta `languages` para criar traduções.

## Como Contribuir

1. Faça um fork deste repositório.
2. Crie uma branch para sua feature ou correção (`git checkout -b minha-feature`).
3. Faça commit das suas alterações (`git commit -am 'Adicionei uma nova feature'`).
4. Faça push para a branch (`git push origin minha-feature`).
5. Abra um Pull Request.

## Licença

Este plugin é distribuído sob a licença GPL v2 ou posterior. Para mais informações, leia o arquivo LICENSE.txt.
