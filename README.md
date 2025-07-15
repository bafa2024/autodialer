# AutoDial Pro - Enterprise Auto Dialer Backend

A comprehensive PHP backend for the AutoDial Pro autodialer system, built with OOP, MVC architecture, advanced security features, and real-time capabilities.

## 🚀 Features

### Core Features
- **Advanced Authentication & Security**
  - JWT token-based authentication
  - Password hashing with Argon2id
  - CSRF protection
  - Rate limiting
  - Input sanitization and validation
  - Role-based access control

- **Real-time Communication**
  - WebSocket server for live updates
  - Real-time call status updates
  - Live agent status monitoring
  - Instant notifications
  - Live analytics dashboard

- **Campaign Management**
  - Multiple dialing modes (Predictive, Progressive, Preview, Power)
  - Contact list management
  - Call disposition tracking
  - Campaign analytics and reporting

- **AI Integration**
  - Call summarization using OpenAI
  - Sentiment analysis
  - Voice recognition
  - AI-powered agents
  - Intelligent call routing

- **CRM Integration**
  - Multi-CRM support (Salesforce, HubSpot, Pipedrive, etc.)
  - Bidirectional data sync
  - Contact import/export
  - Lead management

- **Email Campaigns**
  - Email campaign creation and management
  - Template system
  - Batch sending with rate limiting
  - Email analytics and tracking

### Technical Features
- **Modern PHP Architecture**
  - PHP 8.0+ with strict typing
  - PSR-4 autoloading
  - Dependency injection
  - Service-oriented architecture

- **Database Layer**
  - PDO with prepared statements
  - Connection pooling
  - Transaction support
  - Query optimization and logging

- **Security Features**
  - JWT token management
  - Password strength validation
  - Session security
  - XSS and SQL injection protection
  - Rate limiting and DDoS protection

- **API Design**
  - RESTful API endpoints
  - JSON request/response format
  - Comprehensive error handling
  - API versioning support
  - CORS configuration

## 📋 Requirements

- PHP 8.0 or higher
- MySQL 8.0 or higher
- Composer
- WebSocket support (for real-time features)
- SSL certificate (for production)

### PHP Extensions
- ext-json
- ext-mbstring
- ext-pdo
- ext-openssl
- ext-curl
- ext-sodium (recommended)

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd autodialer
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Configuration
```bash
# Copy the example environment file
cp env.example .env

# Edit the .env file with your configuration
nano .env
```

### 4. Database Setup
```bash
# Create the database
mysql -u root -p -e "CREATE DATABASE autodialer_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Create database user
mysql -u root -p -e "CREATE USER 'autodialer_user'@'localhost' IDENTIFIED BY 'your_secure_password';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON autodialer_dev.* TO 'autodialer_user'@'localhost';"
mysql -u root -p -e "FLUSH PRIVILEGES;"

# Run database migrations
mysql -u autodialer_user -p autodialer_dev < database/migrations/001_create_users_table.sql
```

### 5. Create Required Directories
```bash
mkdir -p logs storage/uploads storage/recordings
chmod 755 storage storage/uploads storage/recordings logs
```

### 6. Configure Web Server

#### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName autodialer.local
    DocumentRoot /path/to/autodialer
    
    <Directory /path/to/autodialer>
        AllowOverride All
        Require all granted
    </Directory>
    
    # API routes
    RewriteEngine On
    RewriteRule ^api/(.*)$ api/index.php [QSA,L]
    
    # WebSocket proxy (if using Apache)
    ProxyPass /ws ws://localhost:8080
    ProxyPassReverse /ws ws://localhost:8080
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name autodialer.local;
    root /path/to/autodialer;
    index index.php index.html;

    # API routes
    location /api/ {
        try_files $uri $uri/ /api/index.php?$query_string;
    }

    # WebSocket proxy
    location /ws {
        proxy_pass http://localhost:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🚀 Starting the Application

### 1. Start the WebSocket Server (for real-time features)
```bash
php start_server.php
```

### 2. Access the Application
- Frontend: `http://autodialer.local`
- API: `http://autodialer.local/api/`
- WebSocket: `ws://autodialer.local:8080`

### 3. Default Login Credentials
- Email: `admin@autodialpro.com`
- Password: `Admin123!`

## 📚 API Documentation

### Authentication Endpoints

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123",
    "remember": true
}
```

#### Register
```http
POST /api/auth/register
Content-Type: application/json

{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "password": "SecurePass123!",
    "company": "Example Corp"
}
```

### Campaign Management

#### Create Campaign
```http
POST /api/campaigns
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "Q1 Sales Campaign",
    "description": "Outbound sales campaign for Q1",
    "type": "outbound",
    "dialing_mode": "predictive",
    "dialing_ratio": 2,
    "max_concurrent_calls": 10
}
```

#### Get Campaigns
```http
GET /api/campaigns
Authorization: Bearer <token>
```

### Real-time WebSocket Events

#### Connect to WebSocket
```javascript
const ws = new WebSocket('ws://localhost:8080');

// Authenticate
ws.send(JSON.stringify({
    type: 'auth',
    token: 'your-jwt-token'
}));

// Join call status room
ws.send(JSON.stringify({
    type: 'join_room',
    room: 'calls'
}));

// Listen for events
ws.onmessage = function(event) {
    const data = JSON.parse(event.data);
    console.log('Received:', data);
};
```

## 🔧 Configuration

### Environment Variables

Key configuration options in `.env`:

```env
# Database
DB_HOST=localhost
DB_DATABASE=autodialer_dev
DB_USERNAME=autodialer_user
DB_PASSWORD=your_secure_password

# Security
JWT_SECRET=your-super-secret-jwt-key
JWT_EXPIRATION=3600

# Real-time
WEBSOCKET_PORT=8080
PUSHER_APP_ID=your-pusher-app-id

# VoIP Integration
TWILIO_ACCOUNT_SID=your-twilio-sid
TWILIO_AUTH_TOKEN=your-twilio-token

# AI Services
OPENAI_API_KEY=your-openai-key
```

### Feature Flags

Control feature availability:

```env
FEATURE_AI_AGENTS=true
FEATURE_CALL_RECORDING=true
FEATURE_CRM_INTEGRATION=true
FEATURE_EMAIL_CAMPAIGNS=true
```

## 🔒 Security Features

### Authentication & Authorization
- JWT token-based authentication
- Role-based access control (Admin, Manager, Agent, Viewer)
- Password strength validation
- Account lockout protection
- Session timeout management

### Data Protection
- Input sanitization and validation
- SQL injection prevention
- XSS protection
- CSRF token validation
- Rate limiting on all endpoints

### Encryption
- Password hashing with Argon2id
- JWT token encryption
- Data encryption at rest (configurable)
- HTTPS enforcement (production)

## 📊 Monitoring & Logging

### Log Files
- `logs/auth.log` - Authentication events
- `logs/database.log` - Database queries and errors
- `logs/websocket.log` - WebSocket connections and events
- `logs/router.log` - API request routing
- `logs/models.log` - Model operations

### Performance Monitoring
- Query execution time tracking
- Database connection pooling
- Memory usage monitoring
- Response time logging

## 🧪 Testing

### Unit Tests
```bash
# Run PHPUnit tests
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit --testsuite auth
```

### API Testing
```bash
# Test API endpoints
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@autodialpro.com","password":"Admin123!"}'
```

## 🚀 Deployment

### Production Checklist
- [ ] Update `.env` with production settings
- [ ] Set `APP_ENV=production`
- [ ] Configure SSL certificate
- [ ] Set up database backups
- [ ] Configure monitoring and logging
- [ ] Set up load balancing (if needed)
- [ ] Configure firewall rules
- [ ] Set up automated deployments

### Docker Deployment
```dockerfile
FROM php:8.0-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Create an issue on GitHub
- Email: support@autodialpro.com
- Documentation: [docs.autodialpro.com](https://docs.autodialpro.com)

## 🔄 Changelog

### Version 2.0.0
- Complete rewrite with modern PHP 8.0+
- Advanced security features
- Real-time WebSocket support
- AI integration
- Comprehensive API
- Multi-CRM support
- Email campaign management

### Version 1.0.0
- Initial release
- Basic autodialer functionality
- Simple authentication
- Campaign management
