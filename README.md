# Base G PIX Doação Plugin

> Um plugin simples para adicionar um botão de doação via PIX no seu site WordPress.

## Funcionalidades
- Adiciona um botão de doação via PIX com valores sugeridos e valor personalizado
- Gera QR Code e código "Copia e Cola" automaticamente
- Modal responsivo e personalizável
- Configuração fácil pelo painel do WordPress
- Baseado em Bootstrap


## Instalação
1. Faça upload da pasta do plugin para o diretório `wp-content/plugins/` do seu WordPress
2. Ative o plugin no painel de administração
3. Acesse **Configurações > Base G PIX** para configurar a chave PIX, beneficiário e outras opções

## Como usar
Adicione o botão de doação em qualquer página, post ou widget usando o shortcode:

```
[base_g_pix]
```

### Personalização do botão
- Texto do botão:
	```
	[base_g_pix text="Apoie nosso projeto"]
	```
- Classe CSS personalizada:
	```
	[base_g_pix class="minha-classe"]
	```

## Implementações Futuras
Para que o plugin fique mais modular e personalizável:
- Configuração do acordeon ("Quem é Gabriel Gortan") pelo painel administrativo:
  - Habilitar/desabilitar exibição do acordeon
  - Editar título e mensagem exibida no acordeon
- Configuração dos textos de exibição do modal pelo painel administrativo:
  - Mensagem de agradecimento
  - Mensagem de instrução
  - Títulos e textos do botão/modal
- Outras melhorias

## Créditos
Desenvolvido por [Base G](https://baseg.com.br/) — Gabriel Gortan