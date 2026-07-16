#include <iostream>

// Print numbers from 10 to 0
int main()
{
    int number = 10;

    while (number >= 0) {
        std::cout << number << std::endl;
        --number;
    }

    return 0;
}
