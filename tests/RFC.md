# Changes Required

| **File**                      | **Method**     | **Issue**                                                | **Line** | **Description**                                                                                     |
|-------------------------------|----------------|----------------------------------------------------------|----------|-----------------------------------------------------------------------------------------------------|
# Changes Required

| `MetaDataController.php`      | `index`        | Unknown named parameter `$filters`                        | 84       | The `findAll` method in the `MetaDataMapper` class does not accept named parameters.                |
| `MetaDataController.php`      | `show`         | Incompatible return type                                  | 81       | The `find` method should return an instance of `MetaData`, not an array.                            |
| `MetaDataController.php`      | `create`       | Incompatible return type                                  | 114      | The `createFromArray` method should return an instance of `MetaData`, not an array.                 |
| `MetaDataController.php`      | `update`       | Incompatible return type                                  | 127      | The `updateFromArray` method should return an instance of `MetaData`, not an array.                 |
| `MetaDataController.php`      | `destroy`      | Incompatible return type                                  | 160      | The `find` method should return an instance of `MetaData`, not an array.                            |
| `DirectoryController.php`     | `index`        | Unknown named parameter `$filters`                        | 122      | The `findAll` method in the `ListingMapper` class does not accept named parameters.                 |
| `DirectoryController.php`     | `show`         | Incompatible return type                                  | 84       | The `find` method should return an instance of `Listing`, not an array.                             |
| `DirectoryController.php`     | `create`       | Incompatible return type                                  | 120      | The `createFromArray` method should return an instance of `Listing`, not an array.                  |
| `DirectoryController.php`     | `update`       | Incompatible return type                                  | 157      | The `updateFromArray` method should return an instance of `Listing`, not an array.                  |
| `DirectoryController.php`     | `destroy`      | Incompatible return type                                  | 191      | The `find` method should return an instance of `Listing`, not an array.                             |
| `PublicationsController.php`  | `index`        | Unknown named parameter `$searchParams`                   | 150      | The `findAll` method in the `PublicationMapper` class does not accept named parameters.             |
| `PublicationsController.php`  | `show`         | Incompatible return type                                  | 110      | The `find` method should return an instance of `Publication`, not an array.                         |
| `PublicationsController.php`  | `create`       | Incompatible return type and assertion issues             | 137      | The `createFromArray` method should return an instance of `Publication`, not an array.              |
| `PublicationsController.php`  | `update`       | Incompatible return type and assertion issues             | 166      | The `updateFromArray` method should return an instance of `Publication`, not an array.              |
| `PublicationsController.php`  | `destroy`      | Assertion issues                                          | 196      | Ensure correct assertion for the `destroy` method.                                                  |
| `OrganizationMapper.php`      | `createFromArray` | Method `values` not being called                          | -        | The `values` method call needs to be added to ensure the query builder is correctly set up.         |
| `Listing.php`                 | `jsonSerialize` | Incorrect serialization of fields                         | -        | The `jsonSerialize` method is setting the fields `directory`, `metadata`, `status`, `lastSync`, `default`, and `available` to the value of `search` instead of their actual values. Update the method to correctly serialize these fields based on their actual values. |
| `ListingMapper.php`           | `updateFromArray` | Entity not updated correctly                              | -        | The `updateFromArray` method does not correctly persist changes to the `Listing` entity. The test failure indicates that the `hydrate` method or the `update` call does not correctly set or save the updated values. This results in the updated entity not reflecting the expected values. |
| `OrganizationMapperTest.php`  | `testFind`, `testFindAll`, `testUpdateFromArray` | Errors due to methods that do not exist or are final/static | - | The tests for `OrganizationMapper` have issues related to methods that cannot be configured or do not exist. The problematic lines need to be commented out and addressed separately. |
| `PublicationMapperTest.php`   | `testFind`, `testFindAll`, `testCreateFromArray`, `testUpdateFromArray` | Errors due to undefined methods and null references | - | The tests for `PublicationMapper` have issues related to undefined methods and null references. The problematic lines need to be commented out and addressed separately. |
| `ListingMapperTest.php`       | `testFind`, `testFindAll`, `testUpdateFromArray` | Errors due to methods that do not exist or are final/static | - | The tests for `ListingMapper` have issues related to methods that cannot be configured or do not exist. The problematic lines need to be commented out and addressed separately. |
| `DirectoryController.php`     | `index`        | Unknown named parameter `$filters`                        | 122      | The `findAll` method in the `ListingMapper` class does not accept named parameters. Update the method call to use positional parameters instead. |



## Mappers

| **File**                      | **Method**     | **Issue**                                                | **Line** | **Description**                                                                                     |
|-------------------------------|----------------|----------------------------------------------------------|----------|-----------------------------------------------------------------------------------------------------|
| `OrganizationMapper.php`      | `createFromArray` | Method `values` not being called                          | -        | The `values` method call needs to be added to ensure the query builder is correctly set up.         |
| `ListingMapper.php`           | `updateFromArray` | Entity not updated correctly                              | -        | The `updateFromArray` method does not correctly persist changes to the `Listing` entity. The test failure indicates that the `hydrate` method or the `update` call does not correctly set or save the updated values. This results in the updated entity not reflecting the expected values. |
| `OrganizationMapperTest.php`  | `testFind`, `testFindAll`, `testUpdateFromArray` | Errors due to methods that do not exist or are final/static | - | The tests for `OrganizationMapper` have issues related to methods that cannot be configured or do not exist. The problematic lines need to be commented out and addressed separately. |
| `PublicationMapperTest.php`   | `testFind`, `testFindAll`, `testCreateFromArray`, `testUpdateFromArray` | Errors due to undefined methods and null references | - | The tests for `PublicationMapper` have issues related to undefined methods and null references. The problematic lines need to be commented out and addressed separately. |
| `ListingMapperTest.php`       | `testFind`, `testFindAll`, `testUpdateFromArray` | Errors due to methods that do not exist or are final/static | - | The tests for `ListingMapper` have issues related to methods that cannot be configured or do not exist. The problematic lines need to be commented out and addressed separately. |

## Models

| **File**                      | **Method**     | **Issue**                                                | **Line** | **Description**                                                                                     |
|-------------------------------|----------------|----------------------------------------------------------|----------|-----------------------------------------------------------------------------------------------------|
| `Listing.php`                 | `jsonSerialize` | Incorrect serialization of fields                        | -        | The `jsonSerialize` method is setting the fields `directory`, `metadata`, `status`, `lastSync`, `default`, and `available` to the value of `search` instead of their actual values. Update the method to correctly serialize these fields based on their actual values. |

## Services

| **File**            | **Method**     | **Issue**                                                | **Line** | **Description**                                                                                     |
|---------------------|----------------|----------------------------------------------------------|----------|-----------------------------------------------------------------------------------------------------|
| `ObjectService.php` | `saveObject`   | Inconsistent return type and error handling              | -        | Ensure `saveObject` method returns an array and includes proper error handling.                    |
| `ObjectService.php` | `findObjects`  | Inconsistent return type and error handling              | -        | Ensure `findObjects` method returns an array and includes proper error handling.                   |
| `ObjectService.php` | `findObject`   | Inconsistent return type and error handling              | -        | Ensure `findObject` method returns an array and includes proper error handling.                    |
| `ObjectService.php` | `updateObject` | Inconsistent return type and error handling              | -        | Ensure `updateObject` method returns an array and includes proper error handling.                  |
| `ObjectService.php` | `deleteObject` | Inconsistent return type and error handling              | -        | Ensure `deleteObject` method returns an array and includes proper error handling.                  |






# Explanation for Elasticsearch Tests Needing a Wrapper

Elasticsearch tests often need a wrapper and interface to handle setup and teardown tasks, ensuring a clean state for each test. This approach provides the following benefits:

1. **Environment Management**: It initializes and cleans up the Elasticsearch environment before and after tests to prevent state leakage between tests.
2. **Consistency**: Ensures that each test starts with a known state, making tests reliable and repeatable.
3. **Isolation**: Helps in isolating tests by ensuring that no residual data from previous tests affects the current test.
4. **Error Handling**: Manages exceptions and errors more effectively, providing a controlled environment for handling test failures.
5. **Resource Management**: Properly allocates and deallocates resources, such as connections to the Elasticsearch cluster, ensuring efficient use of resources.
6. **Mocking Final Classes**: By using an interface that the final class implements, we can easily mock dependencies in tests. This is crucial for final classes like `Elastic\Elasticsearch\Client` that cannot be mocked directly.
7. **Dependency Injection**: Encourages good design practices by promoting dependency injection, making the code more modular and easier to test.

By wrapping Elasticsearch tests and using interfaces, we can ensure they are robust, reliable, maintainable, and testable even when dealing with final classes.
