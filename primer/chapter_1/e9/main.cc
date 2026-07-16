#include <iostream>

// Program to sum number from 50 to 100
int main()
{
    int number = 50;
    int sum = 0;

    while (number <= 100) {
        sum += number;
        ++number;
    }

    std::cout << sum << std::endl;

    return 0;
}
