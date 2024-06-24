<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Prático para a função de DevOps</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        h1, h2 {
            color: #333;
        }
        code {
            background-color: #f0f0f0;
            padding: 2px 6px;
            border: 1px solid #ccc;
            font-family: "Courier New", Courier, monospace;
        }
    </style>
</head>
<body>
    <h1>Teste Prático para a função de DevOps</h1>

    <h2>Objetivo:</h2>
    <p>O objetivo deste teste prático é expor os meus conhecimentos em Docker, Nginx, AWS, PHP-FPM, MySQL e GitHub. Nesse desafio, estarei configurando um ambiente básico de desenvolvimento e implantação de um aplicativo PHP usando as tecnologias mencionadas.</p>
 
    <h2>Tarefas:</h2>

    <h3>1. Configuração do ambiente local usando estrutura de microsserviços:</h3>
    <ul>
        <li>Instalação do <a href="https://hub.docker.com/">Docker</a> no ambiente local.</li>
        <li>Criação de um container Docker para executar um servidor web Nginx, na porta 80 (HTTP):</li>
        <code>docker run --name nginx-container -p 80:80 -d nginx:latest</code>
        <li>Criação de um contêiner para o PHP-FPM para receber as requisições do Nginx na porta 9000 para os arquivos .php do projeto:</li>
        <code>docker run --name php-fpm-container -d php:7.4-fpm</code>
        <li>Criação de um arquivo index.php simples que retorne o texto "Olá, mundo!":</li>
        <pre><code>&lt;?php
        echo "Olá, mundo!";
        ?&gt;
        </code></pre>
        <li>Salvar o arquivo como <code>index.php</code> no diretório de trabalho.</li>
        <li>Configuração do Nginx para servir o arquivo PHP, salvando-o como <code>nginx.conf</code>:</li>
        <pre><code>server {
            listen 80;
            server_name localhost;

            root /var/www/html;
            index index.php index.html index.htm;

            location / {
                try_files $uri $uri/ =404;
            }

            location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass php-fpm:9000;
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include fastcgi_params;
            }
        }
        </code></pre>
        <li>Criar um ambiente Dockerfile, baseado na imagem php:7.4-fpm, que tem como objetivo servir aplicações PHP em ambientes de produção:</li>
        <pre><code>FROM php:7.4-fpm
        COPY index.php /var/www/html/
        </code></pre>
        <li>Criar um outro ambiente Dockerfile, para executar o servidor MySQL na versão 8.0:</li>
        <pre><code>FROM mysql:8.0
        ENV MYSQL_ROOT_PASSWORD=senha1234
        EXPOSE 3306
        </code></pre>
        <li>Criar o docker-compose.yml, com o objetivo de definir e configurar múltiplos serviços que serão executados em contêineres Docker, permitindo que se comuniquem e interajam conforme necessário:</li>
        <pre><code>version: '3.7'

        services:
        nginx:
            image: nginx:latest
            ports:
            - "80:80"
            volumes:
            - ./nginx.conf:/etc/nginx/conf.d/default.conf
            - ./index.php:/var/www/html/index.php   
            depends_on:
            - php-fpm
            - mysql

        php-fpm:
            image: php:7.4-fpm
            volumes:
            - ./index.php:/var/www/html/index.php

        mysql:
            image: mysql:8.0
            environment:
            MYSQL_ROOT_PASSWORD: senha1234
            ports:
            - "3306:3306"
        </code></pre>
    </ul>

    <h3>2. Implantação no AWS:</h3>
    <ul>
        <li>Acessar a conta Free Tier na AWS.</li>
        <li>Após fazer o login na sua conta da AWS, busque por EC2 no painel de Gerenciamento da AWS digitando “EC2”. Clique nele, após encontrar.</li>
        <li>No console do EC2, clique em “Launch Instance”. Logo aparecerá uma janela, em que deve ser escolhido um AMI. Para esse desafio técnico, foi escolhido o Amazon Linux, com as configurações padrões de CPU, memória, subnet, tamanho, volume, etc. Não se esqueça de configurar, também, os grupos de segurança (Security), para que possam passar pela porta 80 (HTTP). Por fim, clique em “Review e Launch”.</li>
        <li>Selecione um par de chaves, para um acesso seguro à instância via SSH (secure shell) ou RDP. O arquivo estará com o formato “.pem”. Por fim, lance a instância clicando em “Launch Instances”.</li>
        <li>Para acessar a instância EC2, use o seguinte comando abaixo, usando o terminal:</li>
        <pre><code>ssh -i &lt;caminho do diretório onde está localizado o arquivo “.pem”&gt; ec2-user@&lt;ip da instância&gt;</code></pre>
        <li>Uma vez acessado a instância EC2 via SSH, é hora de fazer a instalação do Docker nela, usando os comandos abaixo:</li>
        <pre><code>sudo yum update
        sudo apt-get install docker
        </code></pre>
        <li>Após a instalação do Docker, execute os seguintes comandos:</li>
        <pre><code>sudo docker start
        sudo usermod -a -G docker ec2-user
        </code></pre>
        <li>Existem diversas formas de implantar o repositório do teste técnico, nesse caso eu optei por usar o Git:</li>
        <pre><code>git clone https://github.com/niklaz4/desafio-tecnico-devops.git
        </code></pre>
        <li>Após clonar o repositório na instância EC2, navegue até o diretório de onde está o diretório e execute o docker-compose.yml. Pronto, os arquivos foram implantados na AWS.</li>
    </ul>

    <h3>Parte 3 (Melhorias no teste):</h3>
    <p>Implementação de um container para o MySQL, que recebe conexões na porta 3306 e o arquivo index.php, com o objetivo de buscar algo do banco de dados. Nesse teste, irá retornar Fatal error: Uncaught Error: Class 'mysqli' not found in /var/www/html/index.php:11 Stack trace: #0 {main} thrown in /var/www/html/index.php on line 11, por não ter encontrado algum banco de dados a ser retornado.</p>
    <p>Para criar um deploy automático no Github Actions, com o objetivo de automatizar e fazer pipelines de CI/CD no processo de implantação de código em uma ambiente de produção como a instância EC2, foi criado um workflow:</p>
    <pre><code>name: Deploy to EC2

    on:
      push:
        branches:
          - main

    jobs:
      deploy:
        runs-on: ubuntu-latest

        steps:
        - name: Check out the repository
          uses: actions/checkout@v2

        - name: Set up SSH
          uses: webfactory/ssh-agent@v0.5.4
          with:
            ssh-private-key: ${{ secrets.EC2_SSH_KEY }}

        - name: Print SSH key fingerprint
          run: ssh-add -l

        - name: Create target directory on EC2
          run: |
            ssh -o StrictHostKeyChecking=no ec2-user@ec2-3-145-34-202.us-east-2.compute.amazonaws.com "mkdir -p /home/ec2-user/desafio-tecnico-devops"

        - name: Copy files via SCP
          run: |
            scp -o StrictHostKeyChecking=no -r nginx.conf index.php docker-compose.yml Dockerfile Dockerfile.mysql ec2-user@ec2-3-145-34-202.us-east-2
  - name: SSH and deploy
      run: |
        ssh -o StrictHostKeyChecking=no ec2-user@ec2-3-145-34-202.us-east-2.compute.amazonaws.com << 'EOF'
          cd /home/ec2-user/desafio-tecnico-devops
          sudo docker-compose down
          sudo docker-compose up -d
        EOF
</code></pre>

<h3>Load Balancer na AWS:</h3>
<p>Para distribuir entre várias instâncias EC2 executando a aplicação em PHP, foi configurado um load balancer na AWS:</p>
<ol>
    <li>Criar um repositório no Elastic Container Registry.</li>
    <li>Buscar por ECR no Gerenciador do AWS.</li>
    <li>Clicar em "Get Started".</li>
    <li>Em Visibility Settings, deixar como private.</li>
    <li>Dar um nome para o repositório (exemplo: "scidesafio-repo").</li>
    <li>No Gerenciador do AWS, buscar por IAM (Identity and Access Management).</li>
    <li>Criar um usuário > Create Roles -> AdministratorAccess.</li>
    <li>Criar uma chave de acesso (Access Key), usando CLI.</li>
    <li>Baixar o arquivo .csv após clicar em next.</li>
    <li>Ir até a sua instância EC2 e digitar no terminal “aws configure”.</li>
    <li>No terminal, inserir as credenciais obtidas no arquivo .csv.</li>
    <li>Inserir a região (exemplo: us-east-1).</li>
    <li>No último campo, apenas dar enter e terminar o seu cadastro de usuário na instância.</li>
    <li>Com o acesso configurado, executar os seguintes comandos:</li>
    <pre><code>aws ecr get-login-password --region us-east-2 | docker login --username AWS --password-stdin 533267002509.dkr.ecr.us-east-2.amazonaws.com
    docker tag scidesafio-repo:latest 533267002509.dkr.ecr.us-east-2.amazonaws.com/scidesafio-repo:latest
    docker push 533267002509.dkr.ecr.us-east-2.amazonaws.com/scidesafio-repo:latest
    </code></pre>
    <li>Finalmente, aplicar o Load Balancer:</li>
    <ul>
        <li>No Gerenciador do AWS, buscar por EC2.</li>
        <li>No painel lateral esquerdo, buscar por “Load Balancers” e clicar nele.</li>
        <li>Em seguida, clicar em “Create” no Application Load Balancer.</li>
        <li>Inserir o nome do Load Balancer e fazer as configurações necessárias.</li>
        <li>Em Listeners and Routing, a porta deve permanecer padrão 80.</li>
        <li>Em Advanced Health Checking, ajustar o Healthing Thredshold conforme necessário.</li>
        <li>Clicar em Next até chegar em Create Load Balancer.</li>
    </ul>
</ol>

<h3>Task Services no ECS (Elastic Container Service):</h3>
<p>Usando ECS, foram criados Task Services para definir instâncias de contêineres para funcionar na aplicação:</p>
<ol>
    <li>No Gerenciador da AWS, digitar ECS.</li>
    <li>Clicar em Task Definitions, no painel lateral esquerdo.</li>
    <li>Na nova janela, clicar em Create a new task definition.</li>
    <li>Em Task definition configuration, inserir o nome da Task Definition Family.</li>
    <li>Dar um nome para o Container-1 e inserir o repositório criado anteriormente no Elastic Container Registry. Lembre-se de deixar a porta padrão como 80 (HTTP) e configurar outros parâmetros necessários.</li>
    <li>Clicar em Next e configurar o ambiente conforme necessário.</li>
    <li>Na página de definição de ambiente, marcar AWS Fargate e AWS EC2 instances, e configurar os Tarks roles conforme descrito.</li>
    <li>Voltar para a aba do ECS, e escolher a Task Role recém-criada em “Task Execution Role”.</li>
    <li>Role até o final da página e clicar em Create.</li>
    <li>Criar um Cluster, clicando em Create Cluster e mantendo as configurações padrões.</li>
    <li>Após criar o Cluster, ir em Create Service. Ajustar as configurações conforme necessário, especialmente o “Desired Tasks” e outros parâmetros.</li>
    <li>Clicar em Create. Pronto, os task services foram concluídos.</li>
</ol>
