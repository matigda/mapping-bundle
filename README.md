Mapping bundle
===========

This bundle uses mapping library to create services for convenient mapping.


Read model use case examples
--------------

Let's say you had data stored in Redis as a JSON and You were mapping it to some ViewDTO. Your code probably looks similar to this:

```php
$jsonProductData = $this->redisConnection->get($itemKey);

$serializedProductData = json_decode($jsonProductData, true);

return new ProductViewDTO(
    $serializedProductData['price']['value'],
    $serializedProductData['price']['currency'],
    $serializedProductData['details']['material'],
    $serializedProductData['details']['sku'],
    $serializedProductData['description'],
    $serializedProductData['productPhotos']
);
```


With this config:

```yml
deetrych_mapping:
    read_models:
        product_view:    # service that will be created will have "deetrych.read_model.mapper.product_view" id
            fields:
                priceValue:
                    path: price.value
                priceCurrency:
                    path: price.currency
                material:
                    path: details.material
                sku:
                    path: details.sku
                description:
                    path: description
                productPhotos:
                    path: productPhotos
            type: json
            model: Vendor\ProductViewDTO
```

Your code will look like that:
```php
return $this->mapper->map($this->redisConnection->get($itemKey));
```



Write model use case examples
--------------

Ok, so let's say You want to save your Domain Model in Redis as json string, but You don't really want to add unnecessary getters in the Model:

```yml
deetrych_mapping:
    write_models:
        product:  # service that will be created will have "deetrych.write_model.mapper.product" id
            fields:
                price.value:
                    path: price.value
                price.currency:
                    path: price.currency
                details.material:
                    path: details.material
                details.sku:
                    path: details.sku
                description:
                    path: details.description
                productPhotos:
                    path: details.productPhotos
            type: json
```
( This config assume that your Product has price and details fields, as well as the fact that those fields are objects which also have given fields)


And your code will look like that:
```php
$this->redisConnection->set($identifier, $this->mapper->map($product));
```


Extending mappers
--------------

When extending mappers You need to provide all the mappers You want to use:

```yml

deetrych_mapping:
    type_map:
        write_models:
            array: Deetrych\Mapping\Mapper\WriteModel\ArrayMapper
            json: Deetrych\Mapping\Mapper\WriteModel\JsonMapper
            elastic: Deetrych\Mapping\Decorator\ElasticMapperDecorator
            mixed: Vendor\YourMapper # <- this class has to extend Deetrych\Mapping\Mapper\WriteModel\AbstractMapper
```
