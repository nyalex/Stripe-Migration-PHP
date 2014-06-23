Stripe-Migration-PHP
====================

These are a collection of scripts that will help you migrate your Stripe data to another Stripe account. Stripe will only migrate customers and cards - nothing else. As a result, I've built these scripts to move other elements: 

- Plans - `move_plans.php`
- Subscriptions - `move_subscriptions.php`

The code provided is intended to be used as a starting point and to be modified to fit your own specific needs. Therefore, I am assuming that you have enough PHP knowledge to go through the code before running it on your Stripe account.

## Instructions

1. Place these files on a PHP server.

2. Add your Stripe API keys into `stripe_keys.php`, but be careful! Test it on your test API keys first!

3. Run the respective file directly in your browser for each element that you want migrated. For example, run `move_plans.php` if you want your plans migrated.
  * **Warning!** When running `move_subscriptions.php`, the script will automatically cancel existing subscriptions at period end from the source account before creating the new one on the destination account.

4. Consider modifying the scripts to migrate only a few subscriptions/plans first as a secondary test.

5. Check your destination account to make sure that everything went through as expected. 

## MIT License

The MIT License (MIT)

Copyright (c) 2014 Alex Markov

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
