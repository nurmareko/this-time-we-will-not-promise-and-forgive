#include <iostream>
#include "Sales_item.h"

int main()
{
    Sales_item current_item, item;

    if (std::cin >> current_item) {
        int count = 1;

        while (std::cin >> item) {
            if (item.isbn() == current_item.isbn()) {
                ++count;
            } else {
                std::cout << current_item.isbn() << " appear " << count << " times" << std::endl;
                current_item = item;
                count = 1;
            }
        }

    std::cout << current_item.isbn() << " appear " << count << " times" << std::endl;
    }


    return 0;
}
