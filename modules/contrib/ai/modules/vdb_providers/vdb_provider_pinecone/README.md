# Pinecone Vector Database Provider

## Overview

This Drupal module provides integration with Pinecone, a managed vector database
service. It includes features for inserting, deleting, and managing vector data.
This module is intended to support the 'Serverless' architecture only. The VDB
provider methods around Collections therefore map to Pinecone's namespace
architecture.

If you intend to use Pinecone Pod infrastructure, that should be provided via
a third-party module implementing a new VDB Provider.

## Requirements

- Pinecone serverless account (starter is fine) and API key.

## Installation

1. **Enable the Module**:
   - Enable the module through Drupal's admin interface (`Extend`), or use 
     Drush:
     ```bash
     drush en vdb_provider_pinecone
     ```

## Configuration

1. **Access the Configuration Form**:
   - Navigate to the module's configuration page at `Configuration` > `AI` > `Vector DBs Settings` > `Configure Pinecone`.

2. **Save Configuration**:
   - Click "Save configuration" to apply your settings.

3. **Use it via Search API**:
   - Select the Pinecone VDB when creating a Search API index.
