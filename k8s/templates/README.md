# Kubernetes Configuration Templates

This directory contains example configuration files for deploying CityNexus to Kubernetes.

## Usage

1. Copy the example files to the parent directory:
   ```bash
   cp citynexus-dev.example.yaml ../citynexus-dev.yaml
   cp citynexus-dev-secret.example.yaml ../citynexus-dev-secret.yaml
   ```

2. Update the copied files with your actual configuration values:
   - Replace placeholder values in `citynexus-dev-secret.yaml` with your actual secrets
   - Modify `citynexus-dev.yaml` as needed for your environment

3. Apply the configurations:
   ```bash
   kubectl apply -f ../citynexus-dev-secret.yaml
   kubectl apply -f ../citynexus-dev.yaml
   ```

## Security Notes

- Never commit the actual secret files to version control
- Keep your secrets secure and only share them through secure channels
- Use environment-specific secrets for different deployments
- Consider using a secrets management solution like HashiCorp Vault for production 