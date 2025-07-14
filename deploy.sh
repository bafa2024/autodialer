#!/bin/bash

# Exit on error
set -e

# Configuration
APP_NAME="autodialer"
REGION="us-west-2"
PLATFORM="PHP 8.2"
ENV_NAME="autodialer-prod"
S3_BUCKET="autodialer-deploy-$(date +%s)"

# Install AWS CLI if not installed
if ! command -v aws &> /dev/null; then
    echo "Installing AWS CLI..."
    curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
    unzip awscliv2.zip
    sudo ./aws/install
    rm -rf awscliv2.zip aws/
fi

# Configure AWS credentials
echo "Configuring AWS credentials..."
aws configure

# Create a zip file for deployment
echo "Creating deployment package..."
zip -r ../$APP_NAME-deploy.zip . -x "*.git*" -x "*.env*" -x "node_modules/*" -x "vendor/*" -x ".ebextensions/*"

# Create S3 bucket
echo "Creating S3 bucket for deployment..."
aws s3 mb s3://$S3_BUCKET --region $REGION

# Upload deployment package to S3
echo "Uploading deployment package to S3..."
aws s3 cp ../$APP_NAME-deploy.zip s3://$S3_BUCKET/

# Create Elastic Beanstalk application
echo "Creating Elastic Beanstalk application..."
aws elasticbeanstalk create-application \
    --application-name $APP_NAME \
    --region $REGION || echo "Application may already exist"

# Create application version
echo "Creating application version..."
aws elasticbeanstalk create-application-version \
    --application-name $APP_NAME \
    --version-label $(date +%Y%m%d%H%M%S) \
    --source-bundle S3Bucket=$S3_BUCKET,S3Key=$APP_NAME-deploy.zip \
    --region $REGION

# Check if environment exists
ENV_EXISTS=$(aws elasticbeanstalk describe-environments \
    --application-name $APP_NAME \
    --environment-names $ENV_NAME \
    --region $REGION \
    --query 'Environments[0].Status' \
    --output text 2>/dev/null || echo "DoesNotExist")

if [ "$ENV_EXISTS" = "DoesNotExist" ] || [ -z "$ENV_EXISTS" ]; then
    # Create new environment
    echo "Creating new environment: $ENV_NAME"
    aws elasticbeanstalk create-environment \
        --application-name $APP_NAME \
        --environment-name $ENV_NAME \
        --region $REGION \
        --platform-arn "arn:aws:elasticbeanstalk:${REGION}::platform/${PLATFORM}/1.0.0" \
        --solution-stack-name "64bit Amazon Linux 2023 v4.1.1 running PHP 8.2" \
        --option-settings file://.ebextensions/02_environment.config
else
    # Update existing environment
    echo "Updating environment: $ENV_NAME"
    aws elasticbeanstalk update-environment \
        --application-name $APP_NAME \
        --environment-name $ENV_NAME \
        --region $REGION \
        --option-settings file://.ebextensions/02_environment.config
fi

# Deploy the application
echo "Deploying application..."
aws elasticbeanstalk update-environment \
    --application-name $APP_NAME \
    --environment-name $ENV_NAME \
    --version-label $(date +%Y%m%d%H%M%S) \
    --region $REGION

echo "Deployment started! Check the AWS Elastic Beanstalk console for progress."
echo "Application URL: $(aws elasticbeanstalk describe-environments \
    --application-name $APP_NAME \
    --environment-names $ENV_NAME \
    --region $REGION \
    --query 'Environments[0].CNAME' \
    --output text)"

# Cleanup
echo "Cleaning up..."
rm -f ../$APP_NAME-deploy.zip

# Note: You might want to keep the S3 bucket for rollback purposes
# Uncomment the following line to delete the S3 bucket
# aws s3 rb s3://$S3_BUCKET --force

echo "Deployment completed successfully!"
