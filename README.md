# High Cohesion Case Study

## 1. Write a class/classes in PHP to represent the above ORDER

Created an `Order.php` class with public readonly promoted constructor properties. Also made the currency values integers representing pence/cents for precision.

## 2. Create a function to parse a JSON file into a PHP ORDER object

Created both `ParserInterface` and `DataFetcherInterface` to separate the concerns of retrieving and parsing data, with a `DataSource` and `DataSourceFactory` to handle composition.

## 3. Imagine that you also need to create a class for PURCHASE_ORDER and it contains the same **title**, **currency** and **total** fields as ORDER, how would you optimise both classes?

Created an abstract `BaseOrder` class encapsulating the shared properties.

### 4. If all the entities from the e-commerce system need a method to return the title field (**getTitle**). Which PHP data structure would you use to make sure this method is required on every new class that we add in our script?

Added a `TitleInterface` for the `Order` and `PurchaseOrder` classes to inherit.

### 5. Create a new class to store a list of ORDERs. Implement a public method to return the list.

Created an `OrderCollection` class implementing the Collection pattern.
