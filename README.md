# AutoDial Pro - AWS Elastic Beanstalk Deployment Guide

This guide will walk you through deploying the AutoDial Pro application to AWS Elastic Beanstalk.

## Prerequisites

1. AWS Account with appropriate permissions
2. AWS CLI installed and configured with access keys
3. Git installed
4. PHP 8.2+ and Composer installed locally for dependency management

## Deployment Steps

### 1. Clone the Repository

```bash
git clone <your-repository-url>
cd autodialer
```

### 2. Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### 3. Configure Environment Variables

Create a `.env` file based on the example:

```bash
cp .env.example .env
```

Update the necessary environment variables in the `.env` file:

```
APP_NAME=AutoDialPro
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_URL=http://your-elasticbeanstalk-url.region.elasticbeanstalk.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=your-rds-endpoint.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=autodialer
DB_USERNAME=db_username
DB_PASSWORD=db_password

# AWS Configuration
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-west-2
AWS_BUCKET=your-s3-bucket-name
```

### 4. Make the Deployment Script Executable

```bash
chmod +x deploy.sh
```

### 5. Deploy to AWS Elastic Beanstalk

Run the deployment script:

```bash
./deploy.sh
```

This script will:
1. Package your application
2. Create/update the Elastic Beanstalk application
3. Deploy to the specified environment
4. Provide you with the application URL

## Post-Deployment Steps

### 1. Set Up a Custom Domain (Optional)

1. Go to AWS Route 53
2. Register a new domain or use an existing one
3. Create a CNAME record pointing to your Elastic Beanstalk URL
4. Update your Elastic Beanstalk environment with the custom domain

### 2. Set Up SSL Certificate

1. Request a certificate in AWS Certificate Manager (ACM)
2. Verify domain ownership
3. Configure the load balancer to use the certificate

### 3. Configure Auto Scaling (Optional)

1. Go to your Elastic Beanstalk environment
2. Navigate to Configuration > Capacity
3. Adjust the minimum and maximum number of instances
4. Set up scaling triggers as needed

## Monitoring and Maintenance

### Accessing Logs

```bash
eb logs
```

### Updating the Application

1. Make your changes locally
2. Commit the changes to Git
3. Run the deployment script again:

```bash
./deploy.sh
```

## Troubleshooting

### Common Issues

1. **Deployment Fails**
   - Check the deployment logs in the Elastic Beanstalk console
   - Verify all required environment variables are set
   - Ensure the IAM role has the necessary permissions

2. **Application Not Starting**
   - Check the error logs in the Elastic Beanstalk console
   - Verify the database connection settings
   - Ensure the document root is correctly set

3. **Environment Variables Not Loading**
   - Verify the `.ebextensions` configuration files
   - Check for typos in variable names
   - Ensure the environment is not in a failed state

## Security Best Practices

1. Never commit sensitive information to version control
2. Use IAM roles instead of access keys when possible
3. Enable encryption at rest and in transit
4. Regularly update dependencies to patch security vulnerabilities
5. Use AWS Secrets Manager or Parameter Store for sensitive configuration

## Cost Optimization

1. Use appropriate instance types based on your workload
2. Set up auto-scaling to handle traffic spikes
3. Use Amazon RDS Proxy for database connections
4. Enable CloudFront for static assets
5. Monitor your AWS costs using AWS Cost Explorer
