<h1>get-your-number</h1>

This is  a Wordpress plug-in (still in development) to help with easy subscription to an event with a maximum of participants.

It's originaly initiated to enable subscription to a spinning event with a maximum of 35 participants. There are more participants who want to subscribe than that there are spinning bikes available and you don't want to use the principle 'who's first with describing gets a bike'. So here's what get-your-number is supposed to do for you when you open subscription.

<ul>
<li>set a range of numbers which overrides the amount of available bikes (e.g. 1 - 100)</li>
<li>let people subscribe using their emailadres and a name</li>
<li>after registration, give the subscriber a random number</li>
<li>close the subscription on a certain date</li>
</ul>

On the subscription page a list of the total of subscribers is shown, with their corresponding number. The numbers do not have to follow up on each other, like in bingo. There is a possibility that someone who subscribed at the last moment gets number 2 and is asured of joining the group of spinners, while an early subscriber got numer 88 and has to wat if no more than 35 subscribers join the subscription. After closing the subscrition a list indicates all given numbers with the name of the sub scriber in an ascending order. In the case of my example the first 35 subscribers in that list will be able to join the group of spinners.

If an email adres already subscribed it's not able to get a second subscription

If for instance 2 bikes are given before the subscription opens, you can set the range of numbers to go from 2 -> 35

v 1.01 BETA
    form included on page or post by schortcode
    added bootstrap classes in html part [todo: possibility for user to uncheck loading of bootstrap and fontawesome]
    custum functions are placed in subfolder /inc
    custom css is included with wp_enque function