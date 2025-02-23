<?php
include './sidebar.php';
include './container-header.php';
$dateFrom = !empty($_GET['date-from']) ? $_GET['date-from'] : date('Y-m-d');
$dateTo = !empty($_GET['date-to']) ? $_GET['date-to'] : date('Y-m-d');
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    eventForSideBar(0);
    setValueHeader("Thống kê");
</script>
<link rel="stylesheet" href="./assets/CSS/statistic.css">

<div class="statistic">
    <div class="statistic__product-sale">
        <p class="statistic-product-sale__header">Thống Kê Sản Phẩm Theo Doanh Số</p>

        <form class="statistic-product-sale__search" method="GET" onsubmit="return checkDateForStatisticSearch();">
            <label for="">Ngày Lọc</label>
            <input name="date-from" type="date" class="product-sale-search__date-from">
            <span class="material-symbols-outlined">arrow_forward</span>
            <input name="date-to" type="date" class="product-sale-search__date-to">
            <script>
                //Set up
                let queryString = window.location.search;
                let params = new URLSearchParams(queryString);
                let date_from = document.querySelector('.product-sale-search__date-from');
                let date_to = document.querySelector('.product-sale-search__date-to');
                date_from.value = currentDate;
                date_to.value = currentDate;

            
                if (params.has("date-from")) {
                    date_from.value = params.get("date-from");
                    date_to.value = params.get("date-to");
                } else {
                    date_from.value = currentDate;
                    date_to.value = currentDate;
                }
            </script>
            <button type="submit" name="submit" class="product-sale-search___btn"><span class="material-symbols-outlined">search</span></button>
        </form>

        <table class="statistic-product-sale__table">
            <thead>
                <th>Thương Hiệu</th>
                <th>Mã Sản Phẩm</th>
                <th>Tên Sản Phẩm</th>
                <th>Hình Ảnh</th>
                <th>Doanh Số (Cái)</th>
                <th>Doanh Thu (VND)</th>
            </thead>
            <tbody>
                <?php
                    $item_per_page = 8;
                    $current_page = !empty($_GET['page']) ? $_GET['page'] : 1;
                    $offset = ($current_page - 1) * $item_per_page;
                    $sql = "select p.ProductID, p.ProductName, p.ProductImg, b.BrandName, SUM(d.Quantity) as `quantity`, SUM(d.UnitPrice * d.Quantity) as `total`
                        from `order` as o, `order_line` as d, `product` as p, `brand` as b
                        where o.OrderID = d.OrderID and d.ProductID = p.ProductID and p.BrandID = b.BrandID and Date(o.OderDate) between '$dateFrom' and '$dateTo'
                        group by p.ProductID";
                    include './connectdb.php';
                    $records = mysqli_query($con, $sql);
                    $num_page = ceil($records->num_rows / $item_per_page);

                    $result = mysqli_query($con, $sql . " order by quantity desc limit {$item_per_page} offset {$offset};");
                    if ($result->num_rows > 0) {
                        while ($row = mysqli_fetch_array($result)) {
                    ?>
                            <tr>
                                <td><?= $row['BrandName'] ?></td>
                                <td><?= $row['ProductID'] ?></td>
                                <td><?= $row['ProductName'] ?></td>
                                <td><img src="./assets/img/productImg/<?= $row['ProductImg'] ?>" alt="Ảnh đồng hồ" style="width: 35px; height: 35px;"></td>
                                <td><?= $row['quantity'] ?></td>
                                <td><?= number_format($row['total']) ?></td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6" style="padding: 16px;">Không có kết quả thống kê nào trong khoảng thời gian này để hiển thị!</td>
                        </tr>
                    <?php
                    }
                    mysqli_close($con);
                ?>
            </tbody>
        </table>

        <div class="paging">
            <?php
            if ($current_page > 3) {
            ?>
                <a href="?page=1&date-from=<?= $dateFrom ?>&date-to=<?= $dateTo ?>" class="paging__item paging__item--hover">First</a>
                <?php
            }
            for ($num = 1; $num <= $num_page; $num++) {
                if ($num != $current_page) {
                    if ($num > $current_page - 3 && $num < $current_page + 3) {
                ?>
                        <a href="?page=<?= $num ?>&date-from=<?= $dateFrom ?>&date-to=<?= $dateTo ?>" class="paging__item paging__item--hover"><?= $num ?></a>
                    <?php
                    }
                } else {
                    ?>
                    <a href="?page=<?= $num ?>&date-from=<?= $dateFrom ?>&date-to=<?= $dateTo ?>" class="paging__item paging__item--active"><?= $num ?></a>
                <?php
                }
            }
            if ($current_page < $num_page - 2) {
                ?>
                <a href="?page=<?= $num_page ?>&date-from=<?= $dateFrom ?>&date-to=<?= $dateTo ?>" class="paging__item paging__item--hover">Last</a>
            <?php
            }
            ?>
        </div>

    </div>

    <div class="modal-statistic">
        <div class="modal-statistic__container">
            <div class="modal-statistic-container__close">
                <span class="material-symbols-outlined">close</span>
            </div>
            <div class="modal-statistic-container__content">
                <p class="modal-statistic-container-content__heading"></p>
                <div class="modal-statistic-container-content_img">
                    <img src="" alt="Ảnh đồng hồ">
                </div>
                <p class="modal-statistic-container-content__name"></p>
                <div style="margin-bottom: 12px;">
                    <label class="modal-statistic-container-content__date">Ngày</label>
                    <label class="modal-statistic-container-content__date-re"></label>
                </div>
                <div>
                    <label class="modal-statistic-container-content__quantity">Tồn kho</label>
                    <label class="modal-statistic-container-content__quantity-re"></label>
                </div>
            </div>
        </div>
    </div>

    <script>
        eventCloseModal('modal-statistic', 'modal-statistic__container', 'modal-statistic-container__close');
    </script>
</div>

<?php include './container-footer.php' ?>