#include <iostream>

// Print numbers in range of x and y, assume x is less than y
int main()
{
    std::cout << "enter value for x and y" << std::endl << ">> ";

    int x, y;
    std::cin >> x >> y;

    while (x <= y) {
        std::cout << x << std::endl;
        ++x;
    }

    return 0;
}
