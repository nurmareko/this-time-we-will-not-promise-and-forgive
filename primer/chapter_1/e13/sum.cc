#include <iostream>

// Program to sum number from 50 to 100
int main()
{
    int sum = 0;

    for (int number = 50; number <= 100; ++number) {
        sum += number;
    }

    std::cout << sum << std::endl;

    return 0;
}
