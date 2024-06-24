<h1> Teste Prático para a função de DevOps</h1>
<h2> Objetivo:</h2></p>
O objetivo deste teste prático é expor os meus conhecimentos em Docker, Nginx, AWS, PHP-FPM, MySQL e GitHub. Nesse desafio, estarei configurando um ambiente básico de desenvolvimento e implantação de um aplicativo PHP usando as tecnologias mencionadas.</p><br>
 
<h2>Tarefas:</h2>
 
<b> 1. Configuração do ambiente local usando estrutura de microsserviços:</b>
- Instalação do <a href="https://hub.docker.com/">Docker</a> no ambiente local.<br>
- Criação de um container Docker para executar um servidor web Nginx, na porta 80 (HTTP):
   <code> docker run --name nginx-container -p 80:80 -d nginx:latest </code></p>
- Criação de um contêiner para o PHP-FPM para receber as requisições do Nginx na porta 9000 para os arquivos .php do projeto:
  <code>  docker run --name php-fpm-container -d php:7.4-fpm</code></p>
- Criação de um arquivo index.php simples que retorne o texto "Olá, mundo!":</p>
- Salvar o arquivo como `index.php` no diretório de trabalho.</p>
- Configuração do Nginx para servir o arquivo PHP, salvando-o como nginx.conf:</p>
- Criar um ambiente Dockerfile, baseado na imagem php:7.4-fpm, que tem como o objetivo de servir aplicações PHP em ambientes de produção: 
- Criar o docker-compose.yml, com o objetivo de definir e configurar múltiplos serviços que serão executados em contêineres Docker, permitindo que se comuniquem e interajam conforme necessário: </p><br>
<b>2. Implantação no AWS:</b></p>
- Acessar a conta Free Tier na AWS.<br>
- Após fazer o login na sua conta da AWS, busque por EC2 no painel de Gerenciamento da AWS digitando “EC2”. Clique nele, após encontrar.<br>
- No console do EC2, clique em “Launch Instance”. Logo aparecerá uma janela, em que deve ser escolhido um AMI. Para esse desafio técnico, foi escolhido o Amazon Linux, com as configurações padrões de CPU, memória, subnet, tamanho, volume, etc. Não se esqueça de configurar, também, os grupos de segurança (Security), para que possam passar pela porta 80 (HTTP). Por fim, clique em “Review e Launch”.<br>
- Selecione um par de chaves, para um acesso seguro à instância via SSH (secure shell) ou RDP. O arquivo estará com o formato “.pem”. Por fim, lance a instância clicando em “Launch Instances”.<br>
- Para acessar a instância EC2, use o seguinte comando abaixo, usando o terminal:
<code> ssh -i "caminho do diretório onde está localizado o arquivo “.pem”" ec2-user@ec2-3-145-34-202.us-east-2.compute.amazonaws.com </code>
Se certifique de conferir o ip nas configurações da instância.<br>
- Uma vez acessado a instância EC2 via SSH, é hora de fazer a instalação do Docker nela, usando os comandos abaixo:
<code>sudo yum update <br>
sudo apt-get install docker</code>
- Após a instalação do Docker, execute os seguintes comandos:
<code>sudo docker start</code> e <code>sudo usermod -a -G docker ec2-user</code>
- Existem diversas formas de implantar o repositório do teste técnico, nesse caso eu optei por usar o Git:
<code> git clone https://github.com/niklaz4/desafio-tecnico-devops.git </code><br>
- Após clonar o repositório na instância EC2, navegue até o diretório de onde está o diretório e execute o docker-compose.yml. Pronto, os arquivos foram implantados na AWS.</p>

<b> 3. Melhorias no teste: </b></p>
- Implementação de um container para o MySQL, que recebe conexões na porta 3306 e o arquivo index.php, com o objetivo de buscar algo do banco de dados. Nesse teste, irá retornar Fatal error: Uncaught Error: Class 'mysqli' not found in /var/www/html/index.php:11 Stack trace: #0 {main} thrown in /var/www/html/index.php on line 11, por não ter encontrado algum banco de dados a ser retornado.<br>
Para criar um deploy automático no Github Actions, com o objetivo de automatizar e fazer pipelines de CI/CD no processo de implantação de código em uma ambiente de produção como a instância EC2, foi criado um workflow.
- Foi configurado um load balancer na AWS para distribuir entre várias instâncias EC2 executando a aplicação em PHP. Para isso, foi necessário efetuar os seguintes passos:<br>
<li>Criar um repositório no Elastic Container Registry.</li>
<li>Buscar por ECR no Gerenciador do AWS;</li>
<li>Clicar em Get Started;</li>
<li>Em Visibility Settings, deixar como private;</li>
<li>Dar um nome para o repositorio. Neste teste, chamamos de “scidesafio-repo”;</li>
<li>No Gerenciador do AWS, buscar por IAM (Identity and Acess Management);</li>
<li>Criar um usuário > Create Roles -> AdministratorAccess;</li>
<li>Criar uma chave de acesso (Access Key), usando CLI;</li>
<li>Baixar o arquivo .csv após clicar em next;</li>
<li>Ir até a sua instância EC2 e digitar no terminal “aws configure”;</li>
<li>No terminal, inserir as credenciais obtidas no arquivo .csv;</li>
<li>Em seguida, inserir a região (us-east-1);</li>
<li>No último campo, apenas dar enter e terminará o seu cadastro de usuário na instância;</p>
- Agora que você está logado, basta seguir os passos a passo:<br>
<li>Inserir no terminal da instância EC2: <code>aws ecr get-login-password --region us-east-2 | docker login --username AWS --password-stdin 533267002509.dkr.ecr.us-east-2.amazonaws.com</code></li>
<li>E fazer a anexação das imagens, usando docker tag: <code> docker tag scidesafio-repo:latest 533267002509.dkr.ecr.us-east-2.amazonaws.com/scidesafio-repo:latest</code></li>
<li>Por fim, o docker push: <code>docker push 533267002509.dkr.ecr.us-east-2.amazonaws.com/scidesafio-repo:latest</code></li></p>
- Finalmente, aplicamos o Load Balancer. Siga o passo a passo:<br>
<li>No Gerenciador do AWS, busque por EC2;</li>
<li>No painel lateral esquerdo, busque por “Load Balancers” e clique nele;</li>
<li>Em seguida, clique em “Create” no Application Load Balancer;</li>
<li>Insira o nome do Load Balancer e faça as devidas configurações, no Networking Mapping e Security Groups (todos foram padrões do projeto). Em Listeners and Routing, a porta deve permanecer padrão 80. Em Advanced Health Checking, diminua o Healthing Thredshold para 2;</li>
<li>Por fim, clique em Next até chegar em Create Load Balancer. </li></p>

- Usando ECS ( Elastic Container Service), foi criado alguns Task Services, com o objetivo de definir instâncias de contêineres para funcionar na aplicação.<br>
<li>No Gerenciador da AWS, digite ECS;</li>
<li>Nesta tela, clique em Task Definitions, no painel lateral esquerdo;</li>
<li>Na nova janela, clique em Create a new task definition;</li>
<li>Em Task definition configuration, inserir o nome da Task Definition Family;</li>
<li>Dê um nome para o Container-1 e insira o repositório criado anteriormente no Elastic Container Registry. Lembre-se de deixar a porta padrão como 80 (HTTP). Clique em Next;</li>
<li>Na próxima página, você deverá definir o ambiente. Nesse caso, marque AWS Fargate e AWS EC2 instances. Para os Tarks roles, será necessário retornar ao IAM, clicar em Roles. Na nova página, deve-se digitar no campo de busca “container”, e aparecerá a opção “Elastic Container Service”. Em seguida, marque a opção Elastic Container Service Task. Clique em Next, e então digite “AMAZONECSTASK” e assinale a opção “AmazonECSTaskExecutionRolePolicy”. Clique em Next novamente e dê um nome para Role.</li>
<li>Volte para a aba do ECS, e escolha a Task Role recém-criada em “Task Execution Role”;</li>
<li>Role até o final da página e clique em criate;</li>
<li>Pronto, agora será necessário criar um Cluster;</li>
<li>Basta clicar em Create Cluster e deixar as configurações padrões;</li>
<li>Após criar o Cluster, vá em Create Service. A maioria das configurações aqui são padrões, a única coisa que deve ser alterado é o “Desired Tasks”, que deve ser 2. Insira os nomes dos security groups e insira o Load Balancer criado anteriormente para este projeto;</li>
<li>Clique em Create. Pronto, os task service foram concluídos;</li></p>Fim.
