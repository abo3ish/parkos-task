

# Your assignment
The purpose of this test is to assess your Laravel skills. It also tests your ability to read specifications and your problem solving skills

### Evaluation Criteria
- Laravel skills
- Problem solving skills
- Ability to read specs

### Parking Reservation REST API

Parkos is moving to Microservices and we want to separate our Reservation system into a dedicated Microservice. It should handle storage for all the reservations and dispatch events to any other systems that have to work with the same reservation.

- Create an API that can do CRUD actions for parking reservations. It must be RESTFUL
- Reservations should contain at least the departure and arrival dates + times as well as the Parking for which the reservation is made
- When the status of a Reservation is changed to “paid”, a Job should be run (asynchronously) that will send en email to the customer
- Keep in mind the best practices of the OWASP Top 10. Since time is limited, choose the ones that you consider doable or most important
- You need to use at least the following technologies and concepts
    - MySQL
    - Laravel (latest stable version)
- You have max 4h of time. 
