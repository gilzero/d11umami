# Milvus Vector Database Provider

## Using with DDEV.

1. Copy the `ddev-example.docker-compose.milvus.yaml` to your `.ddev` folder.
   1. Assuming your project uses the `web` docroot, you can use the below 
      command: 
      ```
      cp web/modules/vdb_providers/vdb_provider_milvus/docs/docker-compose-examples/ddev-example.docker-compose.milvus.yaml .ddev/docker-compose.milvus.yaml
      ```
2. Run `ddev restart` 
3. Access your Milvus UI at `https://{project}.ddev.site:8521`
3. Set up your Milvus Vector Database Plugin configuration to use:
   1. Host: `http://milvus`
   2. Port: `19530`

## Connecting to a hosted Milvus instance.

You can use Zilliz Cloud for example at https://zilliz.com/cloud. To get 
started:
1. Sign up for a free trial if you do not already have a plan
2. Use the credentials provided

# Contributing to the Milvus PHP library dependency.

This provider depends on https://github.com/HelgeSverre/milvus. For developers
wishing to contribute to it:

1. Fork the github repository
2. DDEV config an empty PHP setup
2. Make your code changes
3. Copy the `.env.example` to `.env`
4. Set up the .env with just this:
   ```
   MILVUS_USERNAME="root"
   MILVUS_PASSWORD=""
   MILVUS_HOST=http://milvus
   MILVUS_PORT=19530
   ```
5. Run `ddev exec ./vendor/bin/pest` to run the tests.
6. Make your pull request via the original repository

Note that until https://github.com/HelgeSverre/milvus/pull/5 is merged, the
code changes in that PR are also needed so the tests run.
