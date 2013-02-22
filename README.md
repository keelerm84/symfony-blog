# Symfony 2 Blog #

This is a simple blog written in Symfony 2, initially following the symblog tutorial at http://tutorial.symblog.co.uk/.  Through the mentorship of [Matthew Turland](http://matthewturland.com/), it has progressed into a more [Service Oriented Architecture](http://en.wikipedia.org/wiki/Service-oriented_architecture).

Since Symfony 2 has gained immense popularity in the PHP community, I wanted to explore the framework.  As Symfony bundles are a large part of that ecosystem, I sought out bundled solutions wherever appropriate.  Some of the bundles utilized, outside of the standard framework, include:
* FOS/RestBundle
* Ddeboer/GuzzleBundle
* JMS/SerializeBundle

Having never developed an application using SOA, this approach was decided to expose me to new approaches.  The separation of front-end and back-end paves the way for future development excursions, including mobile app front-ends, or editor integration such as posting new articles.

# Installation Instructions #

```bash
git clone https://github.com/keelerm84/symfony-blog.git
php bin/vendors install
# To run the tests
phpunit -c apps
```
